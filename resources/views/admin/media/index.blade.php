@extends('layouts.admin')

@section('title', 'Gestão de Mídias')
@section('page-title', 'Gestão de Mídias')
@section('page-subtitle', 'Links do Google Drive organizados por cliente, mês e ano')

@section('topbar-actions')
    <button x-data @click="$dispatch('open-modal', 'create-media')" class="btn-primary btn-sm">
        <x-heroicon-m-plus-circle class="w-5 h-5" />
        Novo Link
    </button>
@endsection

@section('content')

    {{-- =============================================
    STATS STRIP
    ============================================= --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-4">
        <div class="card p-4 flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-blue-500/10 flex items-center justify-center shrink-0">
                <x-ri-links-line class="w-4 h-4 text-blue-400" />
            </div>
            <div>
                <p class="text-xs text-ink-muted">Total de Links</p>
                <p class="text-lg font-bold text-ink">{{ $stats['total'] }}</p>
            </div>
        </div>
        <div class="card p-4 flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-green-500/10 flex items-center justify-center shrink-0">
                <x-ri-user-line class="w-4 h-4 text-green-400" />
            </div>
            <div>
                <p class="text-xs text-ink-muted">Clientes com Mídia</p>
                <p class="text-lg font-bold text-ink">{{ $stats['clients_with_media'] }}</p>
            </div>
        </div>
        <div class="card p-4 flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-amber-500/10 flex items-center justify-center shrink-0">
                <x-ri-calendar-line class="w-4 h-4 text-amber-400" />
            </div>
            <div>
                <p class="text-xs text-ink-muted">Mês Atual</p>
                <p class="text-lg font-bold text-ink">{{ $stats['this_month'] }}</p>
            </div>
        </div>
        <div class="card p-4 flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-brand/10 flex items-center justify-center shrink-0">
                <x-ri-calendar-event-fill class="w-4 h-4 text-brand" />
            </div>
            <div>
                <p class="text-xs text-ink-muted">Adicionados Hoje</p>
                <p class="text-lg font-bold text-ink">{{ $stats['today'] }}</p>
            </div>
        </div>
    </div>

    {{-- =============================================
    FILTERS BAR
    ============================================= --}}
    <div class="card p-4 mb-4">
        <form method="GET" action="{{ route('admin.midias.index') }}"
            x-data="{ client: '{{ request('client_id') }}', month: '{{ request('month') }}', year: '{{ request('year') }}' }"
            class="flex flex-wrap items-end gap-3">

            {{-- Cliente --}}
            <div class="form-group flex-1 min-w-40">
                <label class="label">Cliente</label>
                <select name="client_id" x-model="client" class="select">
                    <option value="">Todos os clientes</option>
                    @foreach($clients as $client)
                        <option value="{{ $client->uuid }}">{{ $client->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Mês --}}
            <div class="form-group w-36">
                <label class="label">Mês</label>
                <select name="month" x-model="month" class="select">
                    <option value="">Todos</option>
                    @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}">{{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Ano --}}
            <div class="form-group w-28">
                <label class="label">Ano</label>
                <select name="year" x-model="year" class="select">
                    <option value="">Todos</option>
                    @foreach(range(now()->year, now()->year - 3) as $y)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-2 pb-0.5">
                <button type="submit" class="btn-primary">
                    <x-ri-search-line class="w-4 h-4" />
                    Filtrar
                </button>
                @if(request()->hasAny(['client_id', 'month', 'year']))
                    <a href="{{ route('admin.midias.index') }}" class="btn-secondary">
                        <x-ri-close-line class="w-4 h-4" />
                        Limpar
                    </a>
                @endif
            </div>

        </form>
    </div>

    {{-- =============================================
    GROUPED VIEW — by Client → Year → Month
    ============================================= --}}
    @forelse($mediaByClient as $clientData)
        {{-- Categorias --}}
        @php
            $categories = [
                '' => 'Sem categoria',
                'other' => 'Geral',
                'image' => 'Imagens',
                'video' => 'Vídeos',
                'document' => 'Relatórios',
                'folder' => 'Pasta',
            ];
        @endphp

        <div class="mt-6 mb-6" x-data="{ open: true }">

            {{-- Client Header --}}
            <button @click="open = !open" class="w-full flex items-center justify-between mb-3 group">
                <div class="flex items-center gap-3">
                    {{-- Avatar --}}
                    <div
                        class="w-8 h-8 rounded-full bg-brand-icon border border-brand/30 flex items-center justify-center shrink-0">
                        <span class="text-xs font-bold text-brand">
                            {{ strtoupper(substr($clientData['client']->name, 0, 2)) }}
                        </span>
                    </div>
                    <div class="text-left">
                        <span class="text-sm font-semibold text-ink group-hover:text-brand transition-colors">
                            {{ $clientData['client']->name }}
                        </span>
                        <span class="ml-2 badge badge-gray">
                            {{ $clientData['total'] }} {{ Str::plural('link', $clientData['total']) }}
                        </span>
                    </div>
                </div>
                <x-ri-arrow-down-s-line class="w-4 h-4 text-ink-muted transition-transform duration-200"
                    ::class="open ? '' : '-rotate-90'" />
            </button>

            {{-- Client Media Content --}}
            <div x-show="open" x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">

                <div class="card overflow-hidden">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Descrição</th>
                                <th>Período</th>
                                <th>Link Google Drive</th>
                                <th>Adicionado em</th>
                                <th style="text-align: right !important;">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($clientData['links'] as $media)
                                <tr x-data="{ menuOpen: false }" class="relative">

                                    {{-- Descrição --}}
                                    <td>
                                        <div class="flex items-center gap-2.5">
                                            <div
                                                class="w-7 h-7 rounded-md bg-[#1FA463]/10 border border-[#1FA463]/20 flex items-center justify-center shrink-0">
                                                <x-ri-drive-line class="w-3.5 h-3.5 text-[#1FA463]" />
                                            </div>
                                            <div>
                                                @if($media->type)
                                                    <span class="text-xs text-ink-subtle capitalize" style="margin-top: -5px;">{{ $categories[$media->type] ?? $media->type }}</span>
                                                @endif
                                                <p class="font-medium text-ink text-sm leading-tight mt-0.5">
                                                    {{ $media->title }}
                                                </p>
                                                @if($media->description)
                                                    <p class="text-xs text-ink-subtle mt-0.5">
                                                        {{ Str::limit($media->description, 50) }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Período --}}
                                    <td>
                                        <div class="flex items-center gap-1.5">
                                            <span class="badge badge-blue">
                                                {{ \Carbon\Carbon::create()->month($media->month)->translatedFormat('M') }}
                                            </span>
                                            <span class="text-xs text-ink-muted">{{ $media->year }}</span>
                                        </div>
                                    </td>

                                    {{-- Link --}}
                                    <td>
                                        <div class="flex items-center gap-2 max-w-sm">
                                            <p class="text-xs text-ink-muted truncate font-mono">
                                                {{ Str::limit($media->url, 45) }}
                                            </p>
                                            <a href="{{ $media->url }}" target="_blank" rel="noopener noreferrer"
                                                class="shrink-0 w-6 h-6 rounded bg-white/5 hover:bg-white/10 border border-white/10 flex items-center justify-center transition-colors"
                                                title="Abrir link">
                                                <x-ri-external-link-line class="w-3 h-3 text-ink-muted" />
                                            </a>
                                        </div>
                                    </td>

                                    {{-- Data --}}
                                    <td>
                                        <span class="text-xs text-ink-muted">
                                            {{ $media->created_at->format('d/m/Y') }}
                                        </span>
                                    </td>

                                    {{-- Actions --}}
                                    <td class="text-right">
                                        <div class="flex items-center justify-end gap-1">
                                            {{-- Edit --}}
                                            <button
                                                @click="$dispatch('open-modal', { name: 'edit-media', data: {{ $media->toJson() }} })"
                                                class="btn-icon w-8 h-8" title="Editar">
                                                <x-ri-pencil-line class="w-4 h-4" />
                                            </button>

                                            {{-- Copy URL --}}
                                            <button x-data
                                                @click="navigator.clipboard.writeText('{{ $media->url }}').then(() => { $el.querySelector('svg').style.color = '#22c55e'; setTimeout(() => $el.querySelector('svg').style.color = '', 1500) })"
                                                class="btn-icon w-8 h-8" title="Copiar link">
                                                <x-ri-file-copy-line class="w-4 h-4" />
                                            </button>

                                            {{-- Delete --}}
                                            <button @click="$dispatch('open-confirm', {
                                                                            title: 'Remover link de mídia',
                                                                            message: 'Esta ação não pode ser desfeita.',
                                                                            action: '{{ route('admin.midias.destroy', $media->uuid) }}',
                                                                            method: 'DELETE'
                                                                        })"
                                                class="btn-icon w-8 h-8 hover:border-brand/40 hover:text-brand" title="Remover">
                                                <x-ri-delete-bin-line class="w-4 h-4" />
                                            </button>
                                        </div>
                                    </td>

                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    @empty
        <div class="empty-state card py-20">
            <div
                class="w-16 h-16 rounded-2xl bg-surface-accent border border-white/[0.07] flex items-center justify-center mb-5">
                <x-ri-drive-line class="w-7 h-7 text-ink-subtle" />
            </div>
            <p class="empty-state-title">Nenhum link de mídia encontrado</p>
            <p class="empty-state-desc mb-5">
                @if(request()->hasAny(['client_id', 'month', 'year']))
                    Nenhum resultado para os filtros aplicados.
                @else
                    Adicione links do Google Drive para os seus clientes.
                @endif
            </p>
            @if(request()->hasAny(['client_id', 'month', 'year']))
                <a href="{{ route('admin.midias.index') }}" class="btn-secondary btn-sm">Limpar filtros</a>
            @else
                <button x-data @click="$dispatch('open-modal', 'create-media')" class="btn-primary btn-sm">
                    <x-ri-add-line class="w-3.5 h-3.5" />
                    Adicionar primeiro link
                </button>
            @endif
        </div>
    @endforelse

    {{-- Pagination --}}
    @if($mediaByClient->hasPages() ?? false)
        <div class="flex justify-center mt-6">
            {{ $mediaByClient->withQueryString()->links('components.pagination') }}
        </div>
    @endif


    {{-- =============================================
    MODAL — Create Media Link
    ============================================= --}}
    <x-modal name="create-media" title="Novo Link de Mídia">
        <form method="POST" action="{{ route('admin.midias.store') }}" x-data="mediaForm()">
            @csrf
            <div class="space-y-4">

                {{-- Título --}}
                <div class="form-group">
                    <label class="label" for="title">Título <span class="text-brand">*</span></label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}"
                        placeholder="Ex: Fotos da Campanha" class="input @error('title') input-error @enderror" required
                        maxlength="255" />
                    @error('title')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Cliente --}}
                <div class="form-group">
                    <label class="label" for="client_id">Cliente <span class="text-brand">*</span></label>
                    <select name="client_id" id="client_id" class="select @error('client_id') input-error @enderror"
                        required>
                        <option value="">Selecione o cliente</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->uuid }}" {{ old('client_id') == $client->uuid ? 'selected' : '' }}>
                                {{ $client->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('client_id')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Descrição --}}
                <div class="form-group">
                    <label class="label" for="description">Descrição</label>
                    <input type="text" name="description" id="description" value="{{ old('description') }}"
                        placeholder="Ex: Fotos da campanha de agosto"
                        class="input @error('description') input-error @enderror" maxlength="150" />
                    @error('description')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Tipo --}}
                <div class="form-group">
                    <label class="label" for="type">Tipo de Conteúdo</label>
                    <select name="type" id="type" class="select">
                        <option value="">Sem categoria</option>
                        <option value="other" {{ old('type') == 'other' ? 'selected' : '' }}>Geral</option>
                        <option value="image" {{ old('type') == 'image' ? 'selected' : '' }}>Imagens</option>
                        <option value="video" {{ old('type') == 'video' ? 'selected' : '' }}>Vídeos</option>
                        <option value="document" {{ old('type') == 'document' ? 'selected' : '' }}>Relatórios</option>
                        <option value="folder" {{ old('type') == 'folder' ? 'selected' : '' }}>Pasta</option>
                    </select>
                </div>

                {{-- Mês e Ano lado a lado --}}
                <div class="grid grid-cols-2 gap-3">
                    <div class="form-group">
                        <label class="label" for="month">Mês <span class="text-brand">*</span></label>
                        <select name="month" id="month" class="select @error('month') input-error @enderror" required>
                            <option value="">Selecione</option>
                            @foreach(range(1, 12) as $m)
                                <option value="{{ $m }}" {{ (old('month', now()->month) == $m) ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                </option>
                            @endforeach
                        </select>
                        @error('month')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="label" for="year">Ano <span class="text-brand">*</span></label>
                        <select name="year" id="year" class="select @error('year') input-error @enderror" required>
                            @foreach(range(now()->year, now()->year - 3) as $y)
                                <option value="{{ $y }}" {{ old('year', now()->year) == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                        @error('year')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- URL Google Drive --}}
                <div class="form-group">
                    <label class="label" for="url">URL do Google Drive <span class="text-brand">*</span></label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <x-ri-drive-line class="w-4 h-4 text-[#1FA463]" />
                        </div>
                        <input type="url" name="url" id="url" x-model="url" value="{{ old('url') }}"
                            placeholder="https://drive.google.com/drive/folders/..."
                            class="input pl-9 @error('url') input-error @enderror" required />
                    </div>
                    @error('url')
                        <p class="form-error">{{ $message }}</p>
                    @enderror

                    {{-- Preview da URL --}}
                    <div x-show="url && url.includes('drive.google.com')" x-transition
                        class="mt-2 flex items-center gap-2 px-3 py-2 rounded-lg bg-green-500/5 border border-green-500/20">
                        <x-ri-checkbox-circle-line class="w-3.5 h-3.5 text-green-400 shrink-0" />
                        <span class="text-xs text-green-400">Link do Google Drive reconhecido</span>
                    </div>
                    <div x-show="url && !url.includes('drive.google.com') && url.startsWith('http')" x-transition
                        class="mt-2 flex items-center gap-2 px-3 py-2 rounded-lg bg-amber-500/5 border border-amber-500/20">
                        <x-ri-alert-line class="w-3.5 h-3.5 text-amber-400 shrink-0" />
                        <span class="text-xs text-amber-400">URL não parece ser do Google Drive</span>
                    </div>
                </div>

            </div>

            {{-- Actions --}}
            <div class="flex justify-end gap-2 mt-6 pt-4 border-t border-white/[0.07]">
                <button type="button" @click="$dispatch('close-modal', 'create-media')" class="btn-secondary">
                    Cancelar
                </button>
                <button type="submit" class="btn-primary">
                    <x-ri-save-line class="w-4 h-4" />
                    Salvar Link
                </button>
            </div>
        </form>
    </x-modal>


    {{-- =============================================
    MODAL — Edit Media Link
    ============================================= --}}
    <div x-data="editMediaModal()" @open-modal.window="if ($event.detail?.name === 'edit-media') open($event.detail.data)">
        <x-modal name="edit-media" title="Editar Link de Mídia" x-bind:open="isOpen">
            <form method="POST" x-bind:action="route">
                @csrf
                @method('PUT')

                <div class="space-y-4">

                    {{-- Título --}}
                    <div class="form-group">
                        <label class="label">Título <span class="text-brand">*</span></label>
                        <input type="text" name="title" x-model="form.title" placeholder="Ex: Fotos da Campanha"
                            class="input" required maxlength="255" />
                    </div>

                    {{-- Descrição --}}
                    <div class="form-group">
                        <label class="label">Descrição</label>
                        <input type="text" name="description" x-model="form.description"
                            placeholder="Ex: Fotos da campanha de agosto" class="input" maxlength="150" />
                    </div>

                    {{-- Tipo --}}
                    <div class="form-group">
                        <label class="label">Tipo de Conteúdo</label>
                        <select name="type" x-model="form.type" class="select">
                            <option value="">Sem categoria</option>
                            <option value="other">Geral</option>
                            <option value="image">Imagens</option>
                            <option value="video">Vídeos</option>
                            <option value="document">Relatórios</option>
                            <option value="folder">Pasta</option>
                        </select>
                    </div>

                    {{-- Mês e Ano --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div class="form-group">
                            <label class="label">Mês <span class="text-brand">*</span></label>
                            <select name="month" x-model="form.month" class="select" required>
                                @foreach(range(1, 12) as $m)
                                    <option value="{{ $m }}">
                                        {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="label">Ano <span class="text-brand">*</span></label>
                            <select name="year" x-model="form.year" class="select" required>
                                @foreach(range(now()->year, now()->year - 3) as $y)
                                    <option value="{{ $y }}">{{ $y }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- URL --}}
                    <div class="form-group">
                        <label class="label">URL do Google Drive <span class="text-brand">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <x-ri-drive-line class="w-4 h-4 text-[#1FA463]" />
                            </div>
                            <input type="url" name="url" x-model="form.url" class="input pl-9" required />
                        </div>
                    </div>

                </div>

                <div class="flex justify-end gap-2 mt-6 pt-4 border-t border-white/[0.07]">
                    <button type="button" @click="$dispatch('close-modal', 'edit-media')" class="btn-secondary">
                        Cancelar
                    </button>
                    <button type="submit" class="btn-primary">
                        <x-ri-save-line class="w-4 h-4" />
                        Atualizar
                    </button>
                </div>
            </form>
        </x-modal>
    </div>


    {{-- =============================================
    MODAL — Confirm Delete
    ============================================= --}}
    <x-modal-confirm />

@endsection


{{-- =============================================
ALPINE.JS SCRIPTS
============================================= --}}
@push('scripts')
    <script>
        function mediaForm() {
            return {
                url: '{{ old('url') }}',
            }
        }

        function editMediaModal() {
            return {
                isOpen: false,
                route: '',
                form: {
                    title: '',
                    description: '',
                    type: '',
                    month: '',
                    year: '',
                    url: '',
                },
                open(data) {
                    this.form.title = data.title ?? '';
                    this.form.description = data.description ?? '';
                    this.form.type = data.type ?? '';
                    this.form.month = data.month;
                    this.form.year = data.year;
                    this.form.url = data.url;
                    this.route = `/admin/midias/${data.uuid}`;
                    this.isOpen = true;
                    this.$dispatch('open-modal', 'edit-media');
                }
            }
        }
    </script>
@endpush