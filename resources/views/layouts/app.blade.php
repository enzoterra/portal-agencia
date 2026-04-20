<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Portal Agência</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="bg-page text-ink font-sans">

    <div class="flex min-h-screen" x-data="{ sidebarOpen: false }">

        {{-- ── OVERLAY MOBILE ──────────────────────────────────── --}}
        <div x-show="sidebarOpen" x-transition:enter="transition-opacity duration-300 ease-out"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity duration-200 ease-in" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" @click="sidebarOpen = false"
            class="fixed inset-0 z-40 bg-black/60 backdrop-blur-sm lg:hidden" style="display: none;"></div>

        {{-- ── SIDEBAR ─────────────────────────────────────────── --}}
        <aside
            class="fixed top-0 right-0 h-full w-72 lg:w-60 bg-surface border-l border-white/[0.07] flex flex-col z-50 transition-transform duration-300 ease-out lg:right-auto lg:left-0 lg:border-l-0 lg:border-r lg:translate-x-0"
            :class="sidebarOpen ? 'translate-x-0' : 'translate-x-full lg:translate-x-0'">

            {{-- Logo --}}
            <div class="flex items-center gap-3 px-5 py-5 border-b border-subtle">
                <div class="w-9 h-9 rounded-xl badge-light-red">
                    <img src="{{ asset('images/obturador.png') }}" alt="Logo">
                </div>
                <div>
                    <div class="text-lg font-bold text-ink font-title">Agência Marketing</div>
                    <div class="text-[10px] text-ink-muted">Portal do Cliente</div>
                </div>

                {{-- Botão fechar sidebar (mobile) --}}
                <button @click="sidebarOpen = false"
                    class="ml-auto lg:hidden btn-icon w-7 h-7 text-ink-muted hover:text-ink" aria-label="Fechar menu">
                    <x-heroicon-o-x-mark class="w-4 h-4" />
                </button>
            </div>

            {{-- Nav --}}
            <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto scrollbar-hide">

                <div class="nav-section-label mb-2">Menu</div>

                <a href="{{ route('cliente.painel') }}" @click="sidebarOpen = false"
                    class="{{ request()->routeIs('cliente.painel') ? 'nav-item-active' : 'nav-item' }}">
                    <x-ri-dashboard-fill class="w-5 h-5" />
                    Dashboard
                </a>

                <a href="{{ route('cliente.relatorios.index') }}" @click="sidebarOpen = false"
                    class="{{ request()->routeIs('cliente.relatorios.*') ? 'nav-item-active' : 'nav-item' }}">
                    <x-heroicon-s-document-chart-bar class="w-5 h-5" />
                    Relatórios
                </a>

                <div class="nav-section-label !mt-4 mb-2">Financeiro</div>

                <a href="{{ route('cliente.financeiro.index') }}" @click="sidebarOpen = false"
                    class="{{ request()->routeIs('cliente.financeiro.*') ? 'nav-item-active' : 'nav-item' }}">
                    <x-heroicon-s-credit-card class="w-5 h-5" />
                    Financeiro
                </a>

                <div class="nav-section-label !mt-4 mb-2">Marketing</div>

                <a href="{{ route('cliente.midias.index') }}" @click="sidebarOpen = false"
                    class="{{ request()->routeIs('cliente.midias.*') ? 'nav-item-active' : 'nav-item' }}">
                    <x-heroicon-s-folder class="w-5 h-5" />
                    Mídias
                </a>

                <a href="{{ route('cliente.calendario.index') }}" @click="sidebarOpen = false"
                    class="{{ request()->routeIs('cliente.calendario.*') ? 'nav-item-active' : 'nav-item' }}">
                    <x-heroicon-s-calendar-date-range class="w-5 h-5" />
                    Calendário
                </a>

            </nav>

            {{-- Footer do sidebar --}}
            <div class="px-3 py-4 border-t border-subtle">
                <div class="flex items-center gap-3 px-3 py-2.5 rounded-xl">
                    <div
                        class="w-8 h-8 rounded-full bg-brand-gradient flex items-center justify-center text-xs font-bold text-white flex-shrink-0">
                        {{ strtoupper(substr(auth()->user()->client?->company_name ?? 'C', 0, 2)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-xs font-semibold text-ink truncate">
                            {{ auth()->user()->client?->trade_name ?? auth()->user()->client?->company_name ?? 'Cliente' }}
                        </div>
                        <div class="text-[10px] text-ink-muted">Cliente</div>
                    </div>
                </div>

                <form method="POST" action="{{ route('logout') }}" class="mt-1">
                    @csrf
                    <button type="submit" class="nav-item w-full text-ink-muted hover:text-brand">
                        <x-ri-logout-box-line class="w-5 h-5" />
                        Sair
                    </button>
                </form>
            </div>

        </aside>

        {{-- ── CONTEÚDO PRINCIPAL ──────────────────────────────── --}}
        <div class="flex-1 flex flex-col lg:ml-60 min-w-0">

            {{-- Topbar --}}
            <header class="topbar">

                {{-- Logo (mobile) --}}
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="lg:hidden w-[90px]" />

                <div class="flex-1 min-w-0 max-lg:hidden">
                    <h1 class="page-title font-semibold text-ink font-title truncate"
                        style="line-height: 1.68rem !important">@yield('page-title')</h1>
                    @hasSection('page-subtitle')
                        <p class="page-subtitle text-ink-muted mt-0.5 truncate">@yield('page-subtitle')</p>
                    @endif
                </div>
                <div class="flex items-center gap-2 ml-3 flex-shrink-0 max-lg:hidden">
                    @yield('topbar-actions')
                </div>

                {{-- Botão hamburguer (mobile) --}}
                <button @click="sidebarOpen = true" class="lg:hidden btn-icon flex-shrink-0" aria-label="Abrir menu">
                    <x-heroicon-o-bars-3 class="w-5 h-5" />
                </button>
            </header>

            {{-- Flash messages --}}
            @if(session('success') || session('error') || $errors->any())
                <div class="px-4 sm:px-6 pt-4">
                    @if(session('success'))
                        <div class="alert-success animate-slide-up">
                            ✓ {{ session('success') }}
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert-error animate-slide-up">
                            ✕ {{ session('error') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert-error animate-slide-up">
                            <ul class="list-disc list-inside text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            @endif

            {{-- Page content --}}
            <main class="flex-1 px-4 sm:px-7 pb-8">
                <div class="flex justify-between mt-6 mb-3 lg:hidden">
                    <div class="w-full">
                        <h1 class="page-title font-semibold text-ink font-title truncate">@yield('page-title')</h1>
                        @hasSection('page-subtitle')
                            <p class="page-subtitle text-ink-muted mt-0.5 truncate">@yield('page-subtitle')</p>
                        @endif
                    </div>
                    <div class="flex items-center gap-2 ml-3 flex-shrink-0">
                        @yield('topbar-actions')
                    </div>
                </div>

                <div class="mt-6">
                    @yield('content')
                </div>
            </main>

        </div>
    </div>

    @stack('scripts')
</body>

</html>