@extends('layouts.admin')

@section('title', 'Usuários e Permissões')
@section('page-title', 'Usuários e Permissões')
@section('page-subtitle', 'Gerencie o acesso ao sistema')

@section('topbar-actions')
    <div x-data="{ open: false }" class="relative">
        <button @click="open = !open" class="btn-primary btn-sm">
            <x-heroicon-m-plus-circle class="w-4 h-4" />
            Novo Usuário
            <x-heroicon-m-chevron-down class="w-3 h-3" />
        </button>
        <div x-show="open" @click.outside="open = false" x-transition class="dropdown-menu right-0 w-52">
            <button type="button" @click="open = false; $dispatch('open-modal', 'create-staff')"
                class="dropdown-item w-full">
                <x-heroicon-o-shield-check class="w-4 h-4" />
                Usuário Admin
            </button>
            <button type="button" @click="open = false; $dispatch('open-modal', 'create-client-user')"
                class="dropdown-item w-full">
                <x-heroicon-o-user class="w-4 h-4" />
                Acesso para Cliente
            </button>
        </div>
    </div>
@endsection

@section('content')

    <div x-data="{ tab: '{{ session('permission_tab', 'staff') }}' }">

        {{-- Tabs --}}
        <div class="flex items-center gap-1 border-b border-white/[0.07] mb-5">
            <button @click="tab = 'staff'"
                :class="tab === 'staff' ? 'border-brand text-ink' : 'border-transparent text-ink-muted hover:text-ink'"
                class="flex items-center gap-2 px-4 py-2.5 text-sm font-medium border-b-2 -mb-px transition-colors">
                <x-heroicon-o-shield-check class="w-4 h-4" />
                Admin
                <span class="badge badge-gray ml-1">{{ $staffCount }}</span>
            </button>
            <button @click="tab = 'clients'"
                :class="tab === 'clients' ? 'border-brand text-ink' : 'border-transparent text-ink-muted hover:text-ink'"
                class="flex items-center gap-2 px-4 py-2.5 text-sm font-medium border-b-2 -mb-px transition-colors">
                <x-heroicon-o-users class="w-4 h-4" />
                Clientes
                <span class="badge badge-gray ml-1">{{ $clientUsersCount }}</span>
            </button>
        </div>

        {{-- =============================================
        TAB — Staff
        ============================================= --}}
        <div x-show="tab === 'staff'" x-transition.opacity>

            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Usuário</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Último acesso</th>
                            <th>Criado em</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($staffUsers as $user)
                                            @php $isSuperAdmin = $user->role === 'super_admin'; @endphp
                                            <tr>

                                                {{-- Avatar + nome + email --}}
                                                <td>
                                                    <div class="flex items-center gap-3">
                                                        <div
                                                            class="w-8 h-8 rounded-full bg-brand-icon border border-brand/20
                                                                                                                                                                                    flex items-center justify-center text-xs font-bold text-brand shrink-0">
                                                            {{ strtoupper(substr($user->name, 0, 2)) }}
                                                        </div>
                                                        <div>
                                                            <div class="text-sm font-medium text-ink flex items-center gap-1.5">
                                                                {{ $user->name }}
                                                                @if($user->id === auth()->id())
                                                                    <span class="badge badge-gray" style="font-size:9px">Você</span>
                                                                @endif
                                                            </div>
                                                            <div class="text-xs text-ink-muted">{{ $user->email }}</div>
                                                        </div>
                                                    </div>
                                                </td>

                                                {{-- Role --}}
                                                <td>
                                                    <span class="badge {{ match ($user->role) {
                                'super_admin' => 'badge-red',
                                'admin' => 'badge-blue',
                                default => 'badge-gray',
                            } }}">
                                                        {{ match ($user->role) {
                                'super_admin' => 'Super Admin',
                                'admin' => 'Admin',
                                default => ucfirst($user->role),
                            } }}
                                                    </span>
                                                </td>

                                                {{-- Status --}}
                                                <td>
                                                    @if($user->is_active)
                                                        <span class="badge badge-green">Ativo</span>
                                                    @else
                                                        <span class="badge badge-gray">Inativo</span>
                                                    @endif
                                                </td>

                                                {{-- Último acesso --}}
                                                <td>
                                                    <span class="text-sm text-ink-muted">
                                                        {{ $user->last_login_at?->diffForHumans() ?? 'Nunca' }}
                                                    </span>
                                                </td>

                                                {{-- Criado em --}}
                                                <td>
                                                    <span class="text-sm text-ink-muted">
                                                        {{ $user->created_at->format('d/m/Y') }}
                                                    </span>
                                                </td>

                                                {{-- Ações --}}
                                                <td>
                                                    @php
                                                        // Admin não pode agir sobre super_admin
                                                        $canAct = auth()->user()->role === 'super_admin'
                                                            || (!$isSuperAdmin && $user->id !== auth()->id());
                                                    @endphp

                                                    <div class="flex items-center gap-1 justify-end">
                                                        @if($canAct && $user->id !== auth()->id())

                                                            {{-- Toggle ativo/inativo --}}
                                                            <form method="POST" action="{{ route('admin.permissoes.alternar', $user->id) }}">
                                                                @csrf @method('PATCH')
                                                                <button type="submit"
                                                                    class="btn-icon {{ $user->is_active ? 'hover:text-amber-400 hover:border-amber-400/30' : 'hover:text-green-400 hover:border-green-400/30' }}"
                                                                    title="{{ $user->is_active ? 'Desativar' : 'Ativar' }}">
                                                                    @if($user->is_active)
                                                                        <x-heroicon-o-pause-circle class="w-4 h-4" />
                                                                    @else
                                                                        <x-heroicon-o-play-circle class="w-4 h-4" />
                                                                    @endif
                                                                </button>
                                                            </form>

                                                            {{-- Editar --}}
                                                            <button type="button" x-data @click="$dispatch('edit-user', {
                                                                                                id:   '{{ $user->id }}',
                                                                                                name: '{{ addslashes($user->name) }}',
                                                                                                email: '{{ $user->email }}',
                                                                                                role: '{{ $user->role }}',
                                                                                                type: 'staff'
                                                                                            })" class="btn-icon" title="Editar usuário">
                                                                <x-heroicon-o-pencil class="w-4 h-4" />
                                                            </button>

                                                            {{-- Remover --}}
                                                            <form method="POST" action="{{ route('admin.permissoes.excluir', $user->id) }}"
                                                                onsubmit="return confirm('Remover {{ addslashes($user->name) }}?')">
                                                                @csrf @method('DELETE')
                                                                <button type="submit" class="btn-icon hover:bg-brand-icon hover:text-brand"
                                                                    title="Remover">
                                                                    <x-heroicon-o-trash class="w-4 h-4" />
                                                                </button>
                                                            </form>

                                                        @else
                                                            <span class="text-xs text-ink-subtle px-2">
                                                                {{ $user->id === auth()->id() ? '—' : 'Sem permissão' }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <div class="empty-state py-12">
                                        <x-heroicon-o-shield-check class="w-10 h-10 text-ink-subtle mx-auto mb-3" />
                                        <p class="empty-state-title">Nenhum usuário admin cadastrado</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- =============================================
        TAB — Usuários clientes
        ============================================= --}}
        <div x-show="tab === 'clients'" x-transition.opacity>

            {{-- Filtro por cliente --}}
            <form method="GET" action="{{ route('admin.permissoes.index') }}" class="flex items-center gap-3 mb-4">
                <input type="hidden" name="tab" value="clients">
                <div class="relative flex-1 max-w-xs">
                    <x-heroicon-o-magnifying-glass
                        class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-ink-subtle" />
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Buscar por nome ou email..." class="input pl-9">
                </div>
                <select name="client_filter" class="select w-44">
                    <option value="">Todos os clientes</option>
                    @foreach($clients as $client)
                        <option value="{{ $client->id }}" {{ request('client_filter') == $client->id ? 'selected' : '' }}>
                            {{ $client->trade_name ?? $client->company_name }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="btn-secondary btn-sm">Filtrar</button>
                @if(request()->hasAny(['search', 'client_filter']))
                    <a href="{{ route('admin.permissoes.index', ['tab' => 'clients']) }}" class="btn-ghost btn-sm">Limpar</a>
                @endif
            </form>

            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Usuário</th>
                            <th>Empresa cliente</th>
                            <th>Status</th>
                            <th>Último acesso</th>
                            <th>Criado em</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($clientUsers as $user)
                            <tr>

                                {{-- Avatar + nome --}}
                                <td>
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-8 h-8 rounded-full bg-white/5 border border-white/10
                                                                                    flex items-center justify-center text-xs font-bold text-ink-muted shrink-0">
                                            {{ strtoupper(substr($user->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-ink">{{ $user->name }}</div>
                                            <div class="text-xs text-ink-muted">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Cliente vinculado --}}
                                <td>
                                    @if($user->client)
                                        <div class="flex items-center gap-2">
                                            <div
                                                class="w-5 h-5 rounded bg-brand-icon flex items-center justify-center
                                                                                                                text-[9px] font-bold text-brand shrink-0">
                                                {{ strtoupper(substr($user->client->company_name, 0, 2)) }}
                                            </div>
                                            <span class="text-sm text-ink">
                                                {{ $user->client->trade_name ?? $user->client->company_name }}
                                            </span>
                                        </div>
                                    @else
                                        <span class="text-xs text-ink-subtle">Sem vínculo</span>
                                    @endif
                                </td>

                                {{-- Status --}}
                                <td>
                                    @if($user->is_active)
                                        <span class="badge badge-green">Ativo</span>
                                    @else
                                        <span class="badge badge-gray">Inativo</span>
                                    @endif
                                </td>

                                {{-- Último acesso --}}
                                <td>
                                    <span class="text-sm text-ink-muted">
                                        {{ $user->last_login_at?->diffForHumans() ?? 'Nunca' }}
                                    </span>
                                </td>

                                {{-- Criado em --}}
                                <td>
                                    <span class="text-sm text-ink-muted">
                                        {{ $user->created_at->format('d/m/Y') }}
                                    </span>
                                </td>

                                {{-- Ações --}}
                                <td>
                                    <div class="flex items-center gap-1 justify-end">

                                        {{-- Toggle ativo/inativo --}}
                                        <form method="POST" action="{{ route('admin.permissoes.alternar', $user->id) }}">
                                            @csrf @method('PATCH')
                                            <button type="submit"
                                                class="btn-icon {{ $user->is_active ? 'hover:text-amber-400 hover:border-amber-400/30' : 'hover:text-green-400 hover:border-green-400/30' }}"
                                                title="{{ $user->is_active ? 'Desativar acesso' : 'Ativar acesso' }}">
                                                @if($user->is_active)
                                                    <x-heroicon-o-pause-circle class="w-4 h-4" />
                                                @else
                                                    <x-heroicon-o-play-circle class="w-4 h-4" />
                                                @endif
                                            </button>
                                        </form>

                                        {{-- Editar --}}
                                        <button type="button" x-data @click="$dispatch('edit-user', {
                                                        id:   '{{ $user->id }}',
                                                        name: '{{ addslashes($user->name) }}',
                                                        email: '{{ $user->email }}',
                                                        client_id: '{{ $user->client_id }}',
                                                        type: 'client'
                                                    })" class="btn-icon" title="Editar usuário">
                                            <x-heroicon-o-pencil class="w-4 h-4" />
                                        </button>

                                        {{-- Remover --}}
                                        <form method="POST" action="{{ route('admin.permissoes.excluir', $user->id) }}"
                                            onsubmit="return confirm('Remover acesso de {{ addslashes($user->name) }}?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn-icon hover:bg-brand-icon hover:text-brand"
                                                title="Remover">
                                                <x-heroicon-o-trash class="w-4 h-4" />
                                            </button>
                                        </form>

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <div class="empty-state py-12">
                                        <x-heroicon-o-users class="w-10 h-10 text-ink-subtle mx-auto mb-3" />
                                        <p class="empty-state-title">Nenhum usuário cliente encontrado</p>
                                        <p class="empty-state-desc mb-4">
                                            @if(request()->hasAny(['search', 'client_filter']))
                                                Nenhum resultado para os filtros aplicados.
                                            @else
                                                Crie um acesso para um cliente existente.
                                            @endif
                                        </p>
                                        @if(request()->hasAny(['search', 'client_filter']))
                                            <a href="{{ route('admin.permissoes.index', ['tab' => 'clients']) }}"
                                                class="btn-secondary btn-sm">Limpar filtros</a>
                                        @else
                                            <button type="button" x-data @click="$dispatch('open-modal', 'create-client-user')"
                                                class="btn-primary btn-sm">
                                                + Novo acesso
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($clientUsers->hasPages())
                <div class="flex items-center justify-between mt-4">
                    <p class="text-xs text-ink-muted">
                        Mostrando {{ $clientUsers->firstItem() }}–{{ $clientUsers->lastItem() }}
                        de {{ $clientUsers->total() }}
                    </p>
                    {{ $clientUsers->withQueryString()->links('vendor.pagination.tailwind') }}
                </div>
            @endif
        </div>

    </div>


    {{-- =============================================
    MODAL — Criar usuário staff
    ============================================= --}}
    <div x-data x-on:open-modal.window="if ($event.detail === 'create-staff') $refs.createStaff.showModal()">
        <dialog x-ref="createStaff" class="card w-full max-w-md p-0 backdrop:bg-black/60 open:animate-fade-in"
            @click.self="$refs.createStaff.close()">

            <div class="flex items-center justify-between px-6 py-4 border-b border-white/[0.07]">
                <h2 class="text-sm font-semibold text-ink">Novo Usuário Admin</h2>
                <button @click="$refs.createStaff.close()" class="btn-ghost btn-sm p-1">
                    <x-heroicon-o-x-mark class="w-4 h-4" />
                </button>
            </div>

            <form method="POST" action="{{ route('admin.permissoes.guardar') }}" class="px-6 py-5 space-y-4">
                @csrf
                <input type="hidden" name="type" value="staff">

                <div class="form-group" style="margin-top: 0">
                    <label class="label">Nome <span class="text-brand">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="Nome completo"
                        class="input @error('name') input-error @enderror" required>
                    @error('name') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="form-group">
                    <label class="label">E-mail <span class="text-brand">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="email@agenciaconti.com.br"
                        class="input @error('email') input-error @enderror" required>
                    @error('email') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="form-group">
                    <label class="label">Role <span class="text-brand">*</span></label>
                    <select name="role" class="select @error('role') input-error @enderror" required>
                        <option value="admin" {{ old('role', 'admin') === 'admin' ? 'selected' : '' }}>Admin</option>
                        {{-- Somente super_admin pode criar outro super_admin --}}
                        @if(auth()->user()->role === 'super_admin')
                            <option value="super_admin" {{ old('role') === 'super_admin' ? 'selected' : '' }}>
                                Super Admin
                            </option>
                        @endif
                    </select>
                    @error('role') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                {{-- Senha definida pelo admin --}}
                <div class="form-group">
                    <label class="label">Senha <span class="text-brand">*</span></label>
                    <div class="relative" x-data="{ show: false }">
                        <input :type="show ? 'text' : 'password'" name="password"
                            placeholder="Mínimo 8 caracteres e 1 número"
                            class="input pr-10 @error('password') input-error @enderror" required minlength="8"
                            pattern="^(?=.*[A-Za-z])(?=.*\d).{8,}$">
                        <button type="button" @click="show = !show"
                            class="absolute inset-y-0 right-0 px-3 text-ink-subtle hover:text-ink transition-colors">
                            <x-heroicon-o-eye class="w-4 h-4" x-show="!show" />
                            <x-heroicon-o-eye-slash class="w-4 h-4" x-show="show" />
                        </button>
                    </div>
                    @error('password') <p class="form-error">{{ $message }}</p> @enderror
                    <p class="text-xs text-ink-subtle mt-1">
                        Informe a senha ao usuário — não há envio por e-mail.
                    </p>
                </div>

                <div class="flex justify-end gap-2 pt-2 border-t border-white/[0.07]">
                    <button type="button" @click="$refs.createStaff.close()" class="btn-secondary">
                        Cancelar
                    </button>
                    <button type="submit" class="btn-primary">
                        <x-heroicon-o-check class="w-4 h-4" /> Criar usuário
                    </button>
                </div>
            </form>
        </dialog>
    </div>


    {{-- =============================================
    MODAL — Criar acesso para cliente
    ============================================= --}}
    <div x-data x-on:open-modal.window="if ($event.detail === 'create-client-user') $refs.createClientUser.showModal()">
        <dialog x-ref="createClientUser" class="card w-full max-w-md p-0 backdrop:bg-black/60 open:animate-fade-in"
            @click.self="$refs.createClientUser.close()">

            <div class="flex items-center justify-between px-6 py-4 border-b border-white/[0.07]">
                <h2 class="text-sm font-semibold text-ink">Novo Acesso para Cliente</h2>
                <button @click="$refs.createClientUser.close()" class="btn-ghost btn-sm p-1">
                    <x-heroicon-o-x-mark class="w-4 h-4" />
                </button>
            </div>

            <form method="POST" action="{{ route('admin.permissoes.guardar') }}" class="px-6 py-5 space-y-4">
                @csrf
                <input type="hidden" name="type" value="client">

                <div class="form-group" style="margin-top: 0">
                    <label class="label">Empresa cliente <span class="text-brand">*</span></label>
                    <select name="client_id" class="select @error('client_id') input-error @enderror" required>
                        <option value="">Selecione...</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}">
                                {{ $client->trade_name ?? $client->company_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('client_id') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="form-group">
                    <label class="label">Nome do usuário <span class="text-brand">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="Ex: João da Silva"
                        class="input @error('name') input-error @enderror" required>
                    @error('name') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="form-group">
                    <label class="label">E-mail <span class="text-brand">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="email@cliente.com.br"
                        class="input @error('email') input-error @enderror" required>
                    @error('email') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="form-group">
                    <label class="label">Senha <span class="text-brand">*</span></label>
                    <div class="relative" x-data="{ show: false }">
                        <input :type="show ? 'text' : 'password'" name="password" placeholder="Mínimo 8 caracteres"
                            class="input pr-10 @error('password') input-error @enderror" required minlength="8">
                        <button type="button" @click="show = !show"
                            class="absolute inset-y-0 right-0 px-3 text-ink-subtle hover:text-ink transition-colors">
                            <x-heroicon-o-eye class="w-4 h-4" x-show="!show" />
                            <x-heroicon-o-eye-slash class="w-4 h-4" x-show="show" />
                        </button>
                    </div>
                    @error('password') <p class="form-error">{{ $message }}</p> @enderror
                    <p class="text-xs text-ink-subtle mt-1">
                        Informe a senha ao cliente — não há envio por e-mail.
                    </p>
                </div>

                <div class="flex justify-end gap-2 pt-2 border-t border-white/[0.07]">
                    <button type="button" @click="$refs.createClientUser.close()" class="btn-secondary">
                        Cancelar
                    </button>
                    <button type="submit" class="btn-primary">
                        <x-heroicon-o-check class="w-4 h-4" /> Criar acesso
                    </button>
                </div>
            </form>
        </dialog>
    </div>


    {{-- =============================================
    MODAL — Editar Usuário
    ============================================= --}}
    <div x-data="editUserModal()" @edit-user.window="open($event.detail)">
        <dialog x-ref="editUser" class="card w-full max-w-md p-0 backdrop:bg-black/60 open:animate-fade-in"
            @click.self="$refs.editUser.close()">

            <div class="flex items-center justify-between px-6 py-4 border-b border-white/[0.07]">
                <h2 class="text-sm font-semibold text-ink">Editar Perfil</h2>
                <button @click="$refs.editUser.close()" class="btn-ghost btn-sm p-1">
                    <x-heroicon-o-x-mark class="w-4 h-4" />
                </button>
            </div>

            <form method="POST" :action="`/admin/permissoes/${userId}`" class="px-6 py-5 space-y-4">
                @csrf
                @method('PUT')
                <input type="hidden" name="type" x-model="userType">

                <div class="flex items-center gap-3 p-3 rounded-xl bg-surface-accent border border-white/[0.07]"
                    style="margin-top: 0">
                    <div class="w-8 h-8 rounded-full bg-brand-icon border border-brand/20
                                            flex items-center justify-center text-xs font-bold text-brand shrink-0"
                        x-text="initials">
                    </div>
                    <span class="text-sm font-medium text-ink" x-text="userName"></span>
                </div>

                <div class="form-group">
                    <label class="label">Nome <span class="text-brand">*</span></label>
                    <input type="text" name="name" x-model="userName" class="input" required>
                </div>

                <div class="form-group">
                    <label class="label">E-mail <span class="text-brand">*</span></label>
                    <input type="email" name="email" x-model="userEmail" class="input" required>
                </div>

                <template x-if="userType === 'staff'">
                    <div class="form-group">
                        <label class="label">Permissão <span class="text-brand">*</span></label>
                        <select name="role" x-model="currentRole" class="select" required>
                            <option value="admin">Admin</option>
                            @if(auth()->user()->role === 'super_admin')
                                <option value="super_admin">Super Admin</option>
                            @endif
                        </select>
                    </div>
                </template>

                <template x-if="userType === 'client'">
                    <div class="form-group">
                        <label class="label">Empresa cliente <span class="text-brand">*</span></label>
                        <select name="client_id" x-model="currentClientId" class="select" required>
                            <option value="">Selecione...</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}">
                                    {{ $client->trade_name ?? $client->company_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </template>

                <div class="form-group">
                    <label class="label">Nova Senha (opcional)</label>
                    <div class="relative" x-data="{ show: false }">
                        <input :type="show ? 'text' : 'password'" name="password"
                            placeholder="Mínimo 8 caracteres e 1 número" class="input pr-10" minlength="8"
                            pattern="^(?=.*[A-Za-z])(?=.*\d).{8,}$">
                        <button type="button" @click="show = !show"
                            class="absolute inset-y-0 right-0 px-3 text-ink-subtle hover:text-ink transition-colors">
                            <x-heroicon-o-eye class="w-4 h-4" x-show="!show" />
                            <x-heroicon-o-eye-slash class="w-4 h-4" x-show="show" />
                        </button>
                    </div>
                    <p class="text-xs text-ink-subtle mt-1">
                        Deixe em branco para não alterar a senha.
                    </p>
                </div>

                <div class="flex justify-end gap-2 pt-2 border-t border-white/[0.07]">
                    <button type="button" @click="$refs.editUser.close()" class="btn-secondary">
                        Cancelar
                    </button>
                    <button type="submit" class="btn-primary">
                        <x-heroicon-o-check class="w-4 h-4" /> Salvar
                    </button>
                </div>
            </form>
        </dialog>
    </div>

@endsection

@push('scripts')
    <script>
        function editUserModal() {
            return {
                userId: '',
                userName: '',
                userEmail: '',
                userType: 'staff',
                currentRole: 'admin',
                currentClientId: '',
                get initials() {
                    return this.userName ? this.userName.substring(0, 2).toUpperCase() : '';
                },
                open(detail) {
                    this.userId = detail.id;
                    this.userName = detail.name;
                    this.userEmail = detail.email;
                    this.userType = detail.type;
                    if (detail.type === 'staff') {
                        this.currentRole = detail.role;
                    } else {
                        this.currentClientId = detail.client_id;
                    }
                    this.$refs.editUser.showModal();
                }
            }
        }

        @if($errors->any())
            document.addEventListener('alpine:init', () => {
                Alpine.nextTick(() => {
                    @if(old('type') === 'staff')
                        window.dispatchEvent(new CustomEvent('open-modal', { detail: 'create-staff' }));
                    @elseif(old('type') === 'client')
                        window.dispatchEvent(new CustomEvent('open-modal', { detail: 'create-client-user' }));
                    @else
                        // Fallback open edit modal if it was an edit error
                        // ...
                    @endif
                        });
            });
        @endif
    </script>
@endpush