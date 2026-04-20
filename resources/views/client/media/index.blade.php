@extends('layouts.app')

@section('title', 'Mídias')
@section('page-title', 'Mídias')
@section('page-subtitle', 'Seus arquivos e pastas do Google Drive organizados por mês')

@section('content')
    <div>

        {{-- =============================================
        FILTRO POR ANO — tabs simples
        ============================================= --}}
        @if($years->count() > 1)
            <div class="flex items-center gap-1 mb-6 border-b border-white/[0.07]">
                <a href="{{ route('client.midias.index') }}" class="px-4 py-2.5 text-sm font-medium border-b-2 -mb-px transition-colors
                        {{ !request('year') ? 'border-brand text-ink' : 'border-transparent text-ink-muted hover:text-ink' }}">
                    Todos
                </a>
                @foreach($years as $year)
                    <a href="{{ route('client.midias.index', ['year' => $year]) }}"
                        class="px-4 py-2.5 text-sm font-medium border-b-2 -mb-px transition-colors
                            {{ request('year') == $year ? 'border-brand text-ink' : 'border-transparent text-ink-muted hover:text-ink' }}">
                        {{ $year }}
                    </a>
                    @endforeach
            </div>
        @endif

        {{-- =============================================
        AGRUPADO POR MÊS
        ============================================= --}}
        @forelse($mediaByMonth as $monthKey => $group)
            @php
                $isCurrentMonth = $loop->first;
            @endphp

            <div class="mb-5" x-data="{ open: {{ $isCurrentMonth ? 'true' : 'false' }} }">
                {{-- Cabeçalho do mês — clicável --}}
                <button @click="open = !open" class="w-full flex items-center justify-between py-2.5 group">
                    <div class="flex items-center gap-3">
                        {{-- Indicador visual do mês --}}
                        <div
                            class="w-8 h-8 rounded-lg bg-brand-icon border border-brand/20 flex items-center justify-center shrink-0">
                            <x-heroicon-o-calendar-days class="w-4 h-4 text-brand" />
                        </div>
                        <div class="text-left">
                            <span class="text-sm font-semibold text-ink group-hover:text-brand transition-colors capitalize">
                                {{ \Carbon\Carbon::parse($monthKey . '-01')->translatedFormat('F \d\e Y') }}
                            </span>
                            <span class="ml-2 badge badge-gray">
                                {{ $group->count() }} {{ $group->count() === 1 ? 'item' : 'itens' }}
                            </span>
                            @if($isCurrentMonth)
                                <span class="ml-1 badge badge-green">Mais recente</span>
                            @endif
                        </div>
                    </div>
                    <x-heroicon-o-chevron-down class="w-4 h-4 text-ink-muted transition-transform duration-200 shrink-0"
                        ::class="open ? '' : '-rotate-90'" />
                </button>

                {{-- Grade de cards --}}
                <div x-show="open" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 pt-3 pb-2">
                    @foreach($group->sortBy('sort_order') as $media)
                        @php
                            $typeConfig = match ($media->type) {
                                'folder' => ['icon' => 'heroicon-o-folder', 'color' => 'text-amber-400', 'bg' => 'bg-amber-500/10', 'border' => 'border-amber-500/20', 'label' => 'Pasta'],
                                'video' => ['icon' => 'heroicon-o-play-circle', 'color' => 'text-purple-400', 'bg' => 'bg-purple-500/10', 'border' => 'border-purple-500/20', 'label' => 'Vídeo'],
                                'image' => ['icon' => 'heroicon-o-photo', 'color' => 'text-blue-400', 'bg' => 'bg-blue-500/10', 'border' => 'border-blue-500/20', 'label' => 'Imagem'],
                                'document' => ['icon' => 'heroicon-o-document-text', 'color' => 'text-green-400', 'bg' => 'bg-green-500/10', 'border' => 'border-green-500/20', 'label' => 'Documento'],
                                'file' => ['icon' => 'heroicon-o-paper-clip', 'color' => 'text-ink-muted', 'bg' => 'bg-white/5', 'border' => 'border-white/10', 'label' => 'Arquivo'],
                                default => ['icon' => 'heroicon-o-link', 'color' => 'text-ink-muted', 'bg' => 'bg-white/5', 'border' => 'border-white/10', 'label' => 'Link'],
                            };
                        @endphp

                        <a href="{{ $media->url }}" target="_blank" rel="noopener noreferrer"
                            class="card group flex flex-col gap-3 p-4 hover:border-white/[0.15] hover:-translate-y-0.5 hover:shadow-card-hover transition-all duration-200 cursor-pointer">
                            {{-- Topo: ícone do tipo + badge --}}
                            <div class="flex items-start justify-between">
                                <div
                                    class="w-10 h-10 rounded-xl {{ $typeConfig['bg'] }} border {{ $typeConfig['border'] }} flex items-center justify-center shrink-0">
                                    <x-dynamic-component :component="$typeConfig['icon']"
                                        class="w-5 h-5 {{ $typeConfig['color'] }}" />
                                </div>
                                {{-- Seta de abertura — aparece no hover --}}
                                <div
                                    class="w-6 h-6 rounded-md bg-white/0 group-hover:bg-white/5 flex items-center justify-center transition-all duration-150">
                                    <x-heroicon-o-arrow-top-right-on-square
                                        class="w-3.5 h-3.5 text-ink-subtle group-hover:text-ink-muted transition-colors" />
                                </div>
                            </div>

                            {{-- Título e descrição --}}
                            <div class="flex-1 min-w-0">
                                <p
                                    class="text-sm font-semibold text-ink group-hover:text-brand transition-colors leading-snug truncate">
                                    {{ $media->title }}
                                </p>
                                @if($media->description)
                                    <p class="text-xs text-ink-muted mt-1 line-clamp-2 leading-relaxed">
                                        {{ $media->description }}
                                    </p>
                                @endif
                            </div>

                            {{-- Rodapé: tipo + data --}}
                            <div class="flex items-center justify-between pt-2 border-t border-white/[0.06]">
                                <span class="text-[10px] font-semibold uppercase tracking-wider {{ $typeConfig['color'] }}">
                                    {{ $typeConfig['label'] }}
                                </span>
                                <span class="text-[10px] text-ink-subtle">
                                    {{ $media->created_at->format('d/m/Y') }}
                                </span>
                            </div>
                        </a>

                    @endforeach
                </div>

                {{-- Separador entre meses --}}
                @if(!$loop->last)
                    <div class="divider mt-4"></div>
                @endif

            </div>
        @empty
            <div class="empty-state card py-20">
                <div
                    class="w-14 h-14 rounded-2xl bg-surface-accent border border-white/[0.07] flex items-center justify-center mb-4">
                    <x-heroicon-o-folder-open class="w-6 h-6 text-ink-subtle" />
                </div>
                <p class="empty-state-title">Nenhuma mídia disponível</p>
                <p class="empty-state-desc">
                    @if(request('year'))
                        Nenhum arquivo encontrado para {{ request('year') }}.
                        <a href="{{ route('client.midias.index') }}" class="text-brand hover:underline">Ver todos</a>
                    @else
                        Seus arquivos e pastas do Google Drive aparecerão aqui assim que forem adicionados.
                    @endif
                </p>
            </div>
        @endforelse

    </div>
@endsection