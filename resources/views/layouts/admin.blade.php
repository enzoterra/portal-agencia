<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') - Portal Agência</title>
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

        {{-- ── SIDEBAR ADMIN ───────────────────────────────────── --}}
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
                    <div class="text-[10px] text-ink-muted">Painel Admin</div>
                </div>

                {{-- Botão fechar sidebar (mobile) --}}
                <button @click="sidebarOpen = false"
                    class="ml-auto lg:hidden btn-icon w-7 h-7 text-ink-muted hover:text-ink" aria-label="Fechar menu">
                    <x-heroicon-o-x-mark class="w-4 h-4" />
                </button>
            </div>

            {{-- Nav --}}
            <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto scrollbar-hide">

                <div class="nav-section-label mb-2">Visão Geral</div>

                <a href="{{ route('admin.painel') }}" @click="sidebarOpen = false"
                    class="{{ request()->routeIs('admin.painel') ? 'nav-item-active' : 'nav-item' }}">
                    <x-ri-dashboard-fill class="w-5 h-5" />
                    Dashboard
                </a>

                <div class="nav-section-label !mt-4 mb-2">Gestão</div>

                <a href="{{ route('admin.clientes.index') }}" @click="sidebarOpen = false"
                    class="{{ request()->routeIs('admin.clientes.*') ? 'nav-item-active' : 'nav-item' }}">
                    <x-heroicon-c-user-group class="w-5 h-5" />
                    Clientes
                </a>

                <a href="{{ route('admin.relatorios.index') }}" @click="sidebarOpen = false"
                    class="{{ request()->routeIs('admin.relatorios.*') ? 'nav-item-active' : 'nav-item' }}">
                    <x-heroicon-s-document-chart-bar class="w-5 h-5" />
                    Relatórios
                </a>

                <a href="{{ route('admin.midias.index') }}" @click="sidebarOpen = false"
                    class="{{ request()->routeIs('admin.midias.*') ? 'nav-item-active' : 'nav-item' }}">
                    <x-heroicon-s-folder class="w-5 h-5" />
                    Mídias
                </a>

                <div class="nav-section-label !mt-4 mb-2">Financeiro</div>

                <a href="{{ route('admin.financeiro.index') }}" @click="sidebarOpen = false"
                    class="{{ request()->routeIs('admin.financeiro.*') ? 'nav-item-active' : 'nav-item' }}">
                    <x-heroicon-s-currency-dollar class="w-5 h-5" />
                    Pagamentos e NFs
                </a>

                <div class="nav-section-label !mt-4 mb-2">Sistema</div>

                <a href="{{ route('admin.permissoes.index') }}" @click="sidebarOpen = false"
                    class="{{ request()->routeIs('admin.permissoes.*') ? 'nav-item-active' : 'nav-item' }}">
                    <x-heroicon-c-key class="w-5 h-5" />
                    Permissões
                </a>

                <a href="{{ route('admin.configuracoes.index') }}" @click="sidebarOpen = false"
                    class="{{ request()->routeIs('admin.configuracoes.*') ? 'nav-item-active' : 'nav-item' }}">
                    <x-ri-settings-4-fill class="w-5 h-5" />
                    Configurações
                </a>

            </nav>

            {{-- Footer --}}
            <div class="px-3 py-4 border-t border-subtle">
                <div class="flex items-center gap-3 px-3 py-2">
                    <div
                        class="w-8 h-8 rounded-full bg-brand-gradient flex items-center justify-center text-xs font-bold text-white flex-shrink-0">
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-xs font-semibold text-ink truncate">{{ auth()->user()->name }}</div>
                        <div class="text-[10px] text-ink-muted capitalize">{{ auth()->user()->role }}</div>
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
                    <h1 class="page-title font-semibold text-ink font-title truncate">@yield('page-title')</h1>
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
                <div class="flex justify-between mt-6 mb-3 gap-3 lg:hidden">
                    <div class="w-52">
                        <h1 class="page-title font-semibold text-ink font-title truncate">@yield('page-title')</h1>
                        @hasSection('page-subtitle')
                            <p class="page-subtitle text-ink-muted mt-0.5 text-wrap">@yield('page-subtitle')</p>
                        @endif
                    </div>
                    <div class="flex items-center justify-end gap-2">
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