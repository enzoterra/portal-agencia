<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Client\Models\Client;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\Traits\HasAuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class PermissionController extends Controller
{
    use HasAuditLog;

    public function index(Request $request)
    {
        // ── Staff (super_admin + admin) ──────────────────────────
        $staffUsers = User::whereIn('role', ['super_admin', 'admin'])
            ->whereNull('client_id')
            ->orderByRaw("FIELD(role, 'super_admin', 'admin')")
            ->orderBy('name')
            ->get();

        // ── Usuários clientes ────────────────────────────────────
        $clientQuery = User::where('role', 'client')
            ->with('client')
            ->when($request->search, fn($q, $v) =>
                $q->where('name',  'like', "%{$v}%")
                  ->orWhere('email', 'like', "%{$v}%")
            )
            ->when($request->client_filter, fn($q, $clientId) =>
                $q->where('client_id', $clientId)
            )
            ->orderBy('name');

        $clientUsers = $clientQuery->paginate(20)->withQueryString();

        // Contadores para os badges das tabs
        $staffCount       = $staffUsers->count();
        $clientUsersCount = User::where('role', 'client')->count();

        // Lista de clientes para os selects dos modais
        $clients = Client::orderBy('company_name')
            ->get(['id', 'uuid', 'company_name', 'trade_name']);

        return view('admin.permissions.index', compact(
            'staffUsers',
            'clientUsers',
            'staffCount',
            'clientUsersCount',
            'clients',
        ))->with('permission_tab', $request->tab ?? session('permission_tab', 'staff'));
    }

    public function store(Request $request)
    {
        $type = $request->input('type'); // 'staff' | 'client'

        // ── Validação base ───────────────────────────────────────
        $rules = [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', Password::min(8)->numbers()],
        ];

        if ($type === 'staff') {
            $allowedRoles = auth()->user()->role === 'super_admin'
                ? ['admin', 'super_admin']
                : ['admin'];

            $rules['role'] = ['required', Rule::in($allowedRoles)];
        } else {
            $rules['client_id'] = ['required', 'integer', 'exists:clients,id'];
        }

        $validated = $request->validate($rules);

        $user = User::create([
            'name'      => $validated['name'],
            'email'     => $validated['email'],
            'password'  => Hash::make($validated['password']),
            'role'      => $type === 'staff' ? $validated['role'] : 'client',
            'client_id' => $type === 'client' ? $validated['client_id'] : null,
            'is_active' => true,
        ]);
        $user->assignRole($user->role);

        $this->recordActivity("Created user ({$user->role})", $user);

        $tab = $type === 'staff' ? 'staff' : 'clients';

        return back()
            ->with('success', 'Usuário criado com sucesso.')
            ->with('permission_tab', $tab);
    }

    public function update(Request $request, User $user)
    {
        // Admin não pode alterar super_admin
        $this->authorizeRoleChange($user);

        $rules = [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', Password::min(8)->numbers()], // Opcional ao editar
        ];

        if ($user->role !== 'client') {
            $allowedRoles = auth()->user()->role === 'super_admin'
                ? ['admin', 'super_admin']
                : ['admin'];
            $rules['role'] = ['required', Rule::in($allowedRoles)];
        } else {
            $rules['client_id'] = ['required', 'integer', 'exists:clients,id'];
        }

        $validated = $request->validate($rules);

        $data = [
            'name'  => $validated['name'],
            'email' => $validated['email'],
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        if ($user->role !== 'client') {
            $data['role'] = $validated['role'];
        } else {
            $data['client_id'] = $validated['client_id'];
        }

        $user->update($data);
        $user->syncRoles($user->role);
        $this->recordActivity("Updated user profile", $user);

        $tab = $user->role === 'client' ? 'clients' : 'staff';

        return back()
            ->with('success', 'Usuário atualizado com sucesso.')
            ->with('permission_tab', $tab);
    }

    public function toggleActive(User $user)
    {
        // Não pode desativar a si mesmo
        abort_if($user->id === auth()->id(), 403, 'Você não pode desativar sua própria conta.');

        // Admin não pode agir sobre super_admin
        $this->authorizeRoleChange($user);

        $user->update(['is_active' => ! $user->is_active]);
        $this->recordActivity($user->is_active ? 'Activated user' : 'Deactivated user', $user);

        $status = $user->is_active ? 'ativado' : 'desativado';

        return back()->with('success', "Usuário {$status} com sucesso.");
    }

    public function destroy(User $user)
    {
        // Não pode remover a si mesmo
        abort_if($user->id === auth()->id(), 403, 'Você não pode remover sua própria conta.');

        // Admin não pode remover super_admin
        $this->authorizeRoleChange($user);

        $this->recordActivity('Deleted user', $user);
        $user->forceDelete(); // HardDelete para remoção definitiva

        $tab = $user->role === 'client' ? 'clients' : 'staff';

        return back()
            ->with('success', 'Usuário removido com sucesso.')
            ->with('permission_tab', $tab);
    }

    // ── Helpers ──────────────────────────────────────────────────

    /**
     * Admin não pode alterar/remover super_admin.
     * Lança 403 se a regra for violada.
     */
    private function authorizeRoleChange(User $target): void
    {
        if (
            auth()->user()->role !== 'super_admin'
            && $target->role === 'super_admin'
        ) {
            abort(403, 'Sem permissão para alterar um Super Admin.');
        }
    }
}
