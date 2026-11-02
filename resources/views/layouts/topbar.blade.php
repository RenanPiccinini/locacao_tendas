<header class="bg-white dark:bg-[#02140e] border-b border-gray-200 dark:border-[#083d2c] sticky top-0 z-30 shadow-xs transition-colors duration-200" x-data="{ mobileOpen: false }">
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
            
            <!-- Botão Menu Mobile (aparece em telas pequenas) -->
            <div class="flex items-center gap-3 lg:hidden">
                <button @click="mobileOpen = !mobileOpen" type="button" class="p-2 rounded-lg text-gray-600 dark:text-emerald-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-[#063325] focus:outline-none transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <a href="{{ route('dashboard') }}" class="font-bold text-lg text-emerald-800 dark:text-emerald-300 flex items-center gap-2">
                    <img src="{{ asset('images/logo.png') }}" alt="Tendas Eldorado" class="w-8 h-8 rounded-full border border-amber-400 object-cover">
                    <span>Tendas <span class="text-amber-500 font-extrabold">Eldorado</span></span>
                </a>
            </div>

            <!-- Título / Espaçador Desktop -->
            <div class="hidden lg:flex items-center gap-2">
                <span class="text-xs font-bold text-emerald-900 dark:text-emerald-300 uppercase tracking-wider">
                    Sistema Operacional de Locações & Montagens
                </span>
                <span class="text-gray-300 dark:text-emerald-800">|</span>
                <span class="text-xs font-semibold text-amber-500">Tendas Eldorado</span>
            </div>

            <!-- Lado Direito: Modo Escuro + Instagram + Perfil -->
            <div class="flex items-center gap-2 sm:gap-3">
                <!-- Botão Alternador Modo Escuro / Claro -->
                <button id="theme-toggle" type="button" class="p-2 text-gray-500 dark:text-amber-400 hover:bg-gray-100 dark:hover:bg-[#063325] rounded-xl text-sm transition" title="Alternar Modo Escuro / Claro">
                    <!-- Ícone Sol (quando estiver escuro) -->
                    <svg id="theme-toggle-light-icon" class="hidden w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" fill-rule="evenodd" clip-rule="evenodd"/>
                    </svg>
                    <!-- Ícone Lua (quando estiver claro) -->
                    <svg id="theme-toggle-dark-icon" class="hidden w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"/>
                    </svg>
                </button>

                <!-- Botão Ver Instagram -->
                <a href="https://www.instagram.com/tendaseldorado/" target="_blank" rel="noopener noreferrer" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-pink-700 dark:text-pink-300 bg-pink-50 dark:bg-pink-950/40 hover:bg-pink-100 dark:hover:bg-pink-900/50 border border-pink-200 dark:border-pink-800/40 transition" title="Ver Instagram Oficial">
                    <svg class="w-3.5 h-3.5 text-pink-600 dark:text-pink-400" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                    </svg>
                    <span>Instagram</span>
                </a>

                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold {{ Auth::user()->isAdmin() ? 'bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-700/60' : 'bg-amber-100 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300 border border-amber-300 dark:border-amber-700/60' }}">
                    {{ Auth::user()->isAdmin() ? 'Administrador' : 'Montador' }}
                </span>

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-lg text-gray-600 dark:text-slate-200 bg-white dark:bg-[#031d14] hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-[#063325] focus:outline-none transition">
                            <div class="w-7 h-7 rounded-full bg-emerald-100 dark:bg-emerald-900/60 text-emerald-800 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-700/60 flex items-center justify-center font-bold text-xs me-2">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                            <div class="hidden sm:block text-left">
                                <div class="text-xs font-bold leading-tight">{{ Auth::user()->name }}</div>
                            </div>
                            <svg class="fill-current h-4 w-4 ms-1 text-gray-400" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            Meu Perfil
                        </x-dropdown-link>

                        <x-dropdown-link href="/" target="_blank">
                            Ver Site Institucional
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                Sair do Sistema
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>
        </div>
    </div>

    <!-- Drawer Mobile (Menu deslizante no celular) -->
    <div x-show="mobileOpen" x-cloak class="relative z-50 lg:hidden" aria-modal="true">
        <!-- Overlay escuro -->
        <div x-show="mobileOpen" 
             x-transition:enter="transition-opacity ease-linear duration-300" 
             x-transition:enter-start="opacity-0" 
             x-transition:enter-end="opacity-100" 
             x-transition:leave="transition-opacity ease-linear duration-300" 
             x-transition:leave-start="opacity-100" 
             x-transition:leave-end="opacity-0" 
             @click="mobileOpen = false" 
             class="fixed inset-0 bg-black/60"></div>

        <!-- Painel Lateral Mobile -->
        <div class="fixed inset-0 flex">
            <div x-show="mobileOpen" 
                 x-transition:enter="transition ease-in-out duration-300 transform" 
                 x-transition:enter-start="-translate-x-full" 
                 x-transition:enter-end="translate-x-0" 
                 x-transition:leave="transition ease-in-out duration-300 transform" 
                 x-transition:leave-start="translate-x-0" 
                 x-transition:leave-end="-translate-x-full" 
                 class="relative mr-16 flex w-full max-w-xs flex-1 bg-[#031d14] text-white flex-col border-r border-[#083d2c]">
                
                <!-- Botão fechar drawer -->
                <div class="absolute left-full top-0 flex w-16 justify-center pt-5">
                    <button @click="mobileOpen = false" type="button" class="-m-2.5 p-2.5 text-white">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Conteúdo Mobile -->
                <div class="h-20 flex items-center px-4 border-b border-[#083d2c] bg-[#02140e] gap-3">
                    <img src="{{ asset('images/logo.png') }}" alt="Tendas Eldorado" class="w-10 h-10 rounded-full object-cover border-2 border-amber-400">
                    <div>
                        <div class="font-black text-white text-base">Tendas <span class="text-amber-400">Eldorado</span></div>
                        <div class="text-[10px] text-emerald-400 font-semibold uppercase">Venda & Locação</div>
                    </div>
                </div>

                <nav class="flex-1 px-4 py-4 space-y-1.5 overflow-y-auto">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('dashboard') ? 'bg-gradient-to-r from-emerald-800 to-emerald-700 text-amber-300 font-bold border-l-4 border-amber-400' : 'text-emerald-100/80 hover:bg-[#073827]' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('pedidos.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('pedidos.*') ? 'bg-gradient-to-r from-emerald-800 to-emerald-700 text-amber-300 font-bold border-l-4 border-amber-400' : 'text-emerald-100/80 hover:bg-[#073827]' }}">
                        Pedidos & O.S.
                    </a>
                    <a href="{{ route('calendario.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('calendario.*') ? 'bg-gradient-to-r from-emerald-800 to-emerald-700 text-amber-300 font-bold border-l-4 border-amber-400' : 'text-emerald-100/80 hover:bg-[#073827]' }}">
                        Calendário
                    </a>

                    @if(Auth::user()->isAdmin())
                        <div class="pt-4 px-3 text-xs uppercase text-amber-400/90 font-bold">Admin</div>
                        <a href="{{ route('montadores.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('montadores.*') ? 'bg-gradient-to-r from-emerald-800 to-emerald-700 text-amber-300 font-bold border-l-4 border-amber-400' : 'text-emerald-100/80 hover:bg-[#073827]' }}">
                            Montadores
                        </a>
                        <a href="{{ route('tendas.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('tendas.*') ? 'bg-gradient-to-r from-emerald-800 to-emerald-700 text-amber-300 font-bold border-l-4 border-amber-400' : 'text-emerald-100/80 hover:bg-[#073827]' }}">
                            Estoque de Tendas
                        </a>
                        <a href="{{ route('clientes.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('clientes.*') ? 'bg-gradient-to-r from-emerald-800 to-emerald-700 text-amber-300 font-bold border-l-4 border-amber-400' : 'text-emerald-100/80 hover:bg-[#073827]' }}">
                            Clientes
                        </a>
                        <a href="{{ route('whatsapp.conexao') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('whatsapp.*') ? 'bg-gradient-to-r from-emerald-800 to-emerald-700 text-amber-300 font-bold border-l-4 border-amber-400' : 'text-emerald-100/80 hover:bg-[#073827]' }}">
                            Conexão WhatsApp
                        </a>
                    @endif

                    <div class="pt-4 border-t border-[#083d2c]">
                        <a href="https://www.instagram.com/tendaseldorado/" target="_blank" rel="noopener noreferrer" class="flex items-center justify-center gap-2 w-full py-2.5 px-3 rounded-lg bg-pink-600/20 text-pink-300 hover:bg-pink-600/30 font-bold text-xs border border-pink-500/30 transition">
                            <span>Instagram @tendaseldorado</span>
                        </a>
                    </div>
                </nav>

                <div class="p-4 border-t border-[#083d2c] bg-[#02140e]">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full py-2 bg-red-600/20 hover:bg-red-600/30 text-red-300 rounded-lg text-sm font-semibold transition">
                            Sair do Sistema
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>
</header>

<!-- Script do Alternador de Modo Escuro / Claro -->
<script>
    (function() {
        const themeToggleBtn = document.getElementById('theme-toggle');
        const darkIcon = document.getElementById('theme-toggle-dark-icon');
        const lightIcon = document.getElementById('theme-toggle-light-icon');

        function syncThemeIcons() {
            if (document.documentElement.classList.contains('dark')) {
                lightIcon?.classList.remove('hidden');
                darkIcon?.classList.add('hidden');
            } else {
                lightIcon?.classList.add('hidden');
                darkIcon?.classList.remove('hidden');
            }
        }

        syncThemeIcons();

        themeToggleBtn?.addEventListener('click', function() {
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('color-theme', 'light');
            } else {
                document.documentElement.classList.add('dark');
                localStorage.setItem('color-theme', 'dark');
            }
            syncThemeIcons();
        });
    })();
</script>
