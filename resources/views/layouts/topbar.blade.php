<header class="bg-white border-b border-gray-200 sticky top-0 z-30 shadow-xs" x-data="{ mobileOpen: false }">
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
            
            <!-- Botão Menu Mobile (aparece em telas pequenas) -->
            <div class="flex items-center gap-3 lg:hidden">
                <button @click="mobileOpen = !mobileOpen" type="button" class="p-2 rounded-lg text-gray-600 hover:text-gray-900 hover:bg-gray-100 focus:outline-none">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <a href="{{ route('dashboard') }}" class="font-bold text-lg text-blue-700 flex items-center gap-2">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21h18M3 10h18M5 6l7-3 7 3M4 10v11m16-11v11" />
                    </svg>
                    <span>Locação Tendas</span>
                </a>
            </div>

            <!-- Título / Espaçador Desktop -->
            <div class="hidden lg:block">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">
                    Sistema Operacional de Locações & Montagens
                </span>
            </div>

            <!-- Lado Direito: Perfil / Ações Rápidas -->
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ Auth::user()->isAdmin() ? 'bg-blue-100 text-blue-800' : 'bg-emerald-100 text-emerald-800' }}">
                    {{ Auth::user()->isAdmin() ? 'Administrador' : 'Montador' }}
                </span>

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-lg text-gray-600 bg-white hover:text-gray-900 hover:bg-gray-50 focus:outline-none transition">
                            <div class="w-7 h-7 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-xs me-2">
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
                 class="relative mr-16 flex w-full max-w-xs flex-1 bg-slate-900 text-white flex-col">
                
                <!-- Botão fechar drawer -->
                <div class="absolute left-full top-0 flex w-16 justify-center pt-5">
                    <button @click="mobileOpen = false" type="button" class="-m-2.5 p-2.5 text-white">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Conteúdo Mobile -->
                <div class="h-16 flex items-center px-6 border-b border-slate-800 bg-slate-950">
                    <span class="font-bold text-lg text-white">Locação de Tendas</span>
                </div>

                <nav class="flex-1 px-4 py-4 space-y-1 overflow-y-auto">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('pedidos.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('pedidos.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                        Pedidos & O.S.
                    </a>
                    <a href="{{ route('calendario.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('calendario.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                        Calendário
                    </a>

                    @if(Auth::user()->isAdmin())
                        <div class="pt-4 px-3 text-xs uppercase text-slate-500 font-bold">Admin</div>
                        <a href="{{ route('montadores.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('montadores.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                            Montadores
                        </a>
                        <a href="{{ route('tendas.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('tendas.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                            Estoque de Tendas
                        </a>
                        <a href="{{ route('clientes.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('clientes.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                            Clientes
                        </a>
                        <a href="{{ route('whatsapp.conexao') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('whatsapp.*') ? 'bg-emerald-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                            Conexão WhatsApp
                        </a>
                    @endif
                </nav>

                <div class="p-4 border-t border-slate-800 bg-slate-950">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full py-2 bg-red-600/20 hover:bg-red-600/40 text-red-300 rounded-lg text-sm font-semibold transition">
                            Sair do Sistema
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>
</header>

