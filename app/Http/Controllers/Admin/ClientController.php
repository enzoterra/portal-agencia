<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Client\Models\Client;
use App\Domain\Client\Services\ClientService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreClientRequest;
use App\Http\Requests\Admin\UpdateClientRequest;
use App\Support\Traits\HasAuditLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ClientController extends Controller
{
    use HasAuditLog;

    public function __construct(private readonly ClientService $service) {}

    public function index(): View
    {
        $clients = Client::withCount(['reports', 'payments'])
            ->when(request('busca'), fn($q, $s) =>
                $q->where('company_name', 'like', "%{$s}%")
                  ->orWhere('trade_name',   'like', "%{$s}%")
                  ->orWhere('email',        'like', "%{$s}%")
            )
            ->when(request('status'), fn($q, $s) => $q->where('status', $s))
            ->orderBy('company_name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.clients.index', compact('clients'));
    }

    public function create(): View
    {
        return view('admin.clients.create');
    }

    public function store(StoreClientRequest $request): RedirectResponse
    {
        $client = $this->service->create($request->validated());
        $this->recordActivity('Created client', $client, null, $client->toArray());

        return redirect()
            ->route('admin.clientes.show', $client)
            ->with('success', 'Cliente criado com sucesso.');
    }

    public function show(Client $client): View
    {
        $client->load([
            'users',
            'payments' => fn($q) => $q->latest()->limit(5),
            'reports'  => fn($q) => $q->latest()->limit(5),
        ]);

        return view('admin.clients.show', compact('client'));
    }

    public function edit(Client $client): View
    {
        return view('admin.clients.edit', compact('client'));
    }

    public function update(UpdateClientRequest $request, Client $client): RedirectResponse
    {
        $oldValues = $client->toArray();
        $updated = $this->service->update($client, $request->validated());
        $this->recordActivity('Updated client', $updated, $oldValues, $updated->toArray());

        return redirect()
            ->route('admin.clientes.show', $client)
            ->with('success', 'Cliente atualizado com sucesso.');
    }

    public function destroy(Client $client): RedirectResponse
    {
        $this->recordActivity('Deleted client', $client, $client->toArray());
        $this->service->delete($client);

        return redirect()
            ->route('admin.clientes.index')
            ->with('success', 'Cliente removido com sucesso.');
    }
}