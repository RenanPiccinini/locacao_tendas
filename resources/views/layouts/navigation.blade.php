<nav x-data="{ open: false }" class="bg-white border-b border-gray-200 sticky top-0 z-40 shadow-sm">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <!-- Logo -->
                <div class="shrink-0 flex items-center gap-2">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2 font-bold text-xl text-blue-700">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21h18M3 10h18M5 6l7-3 7 3M4 10v11m16-11v11" />
                        </svg>
                        <span>Tendas App</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-6 sm:-my-px sm:ms-8 sm:flex items-center">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        Dashboard
                    </x-nav-link>

                    <x-nav-link :href="route('pedidos.index')" :active="request()->routeIs('pedidos.*')">
                        Pedidos / O.S.
                    </x-nav-link>

                    <x-nav-link :href="route('calendario.index')" :active="request()->routeIs('calendario.*')">
                        Calendário
                    </x-nav-link>

                    @if(Auth::user()->isAdmin())
                        <x-nav-link :href="route('tendas.index')" :active="request()->routeIs('tendas.*')">
                            Estoque de Tendas
                        </x-nav-link>

                        <x-nav-link :href="route('clientes.index')" :active="request()->routeIs('clientes.*')">
                            Clientes
                        </x-nav-link>

                        <x-nav-link :href="route('whatsapp.conexao')" :active="request()->routeIs('whatsapp.*')">
                            Conexão WhatsApp
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- Settings & Profile -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-3">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ Auth::user()->isAdmin() ? 'bg-blue-100 text-blue-800' : 'bg-emerald-100 text-emerald-800' }}">
                    {{ Auth::user()->isAdmin() ? 'Administrador' : 'Montador' }}
                </span>

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-600 bg-white hover:text-gray-900 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            Meu Perfil
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                Sair do Sistema
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger Button for Mobile -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-white border-t border-gray-200">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                Dashboard
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('pedidos.index')" :active="request()->routeIs('pedidos.*')">
                Pedidos / O.S.
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('calendario.index')" :active="request()->routeIs('calendario.*')">
                Calendário
            </x-responsive-nav-link>

            @if(Auth::user()->isAdmin())
                <x-responsive-nav-link :href="route('tendas.index')" :active="request()->routeIs('tendas.*')">
                    Estoque de Tendas
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('clientes.index')" :active="request()->routeIs('clientes.*')">
                    Clientes
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('whatsapp.conexao')" :active="request()->routeIs('whatsapp.*')">
                    Conexão WhatsApp
                </x-responsive-nav-link>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 bg-gray-50">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }} ({{ Auth::user()->role }})</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    Meu Perfil
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                        Sair do Sistema
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
