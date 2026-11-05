<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 dark:text-white leading-tight">
                    Painel Principal
                </h2>
                <p class="text-sm text-gray-500 dark:text-emerald-300/70 mt-1">Bem-vindo(a), {{ Auth::user()->name }} ({{ Auth::user()->isAdmin() ? 'Administrador' : 'Montador' }})</p>
            </div>

            <div class="flex items-center gap-2">
                @if(Auth::user()->isAdmin())
                    <a href="{{ route('pedidos.create') }}" class="inline-flex items-center px-4 py-2 bg-emerald-700 hover:bg-emerald-800 text-white font-semibold rounded-lg shadow-sm text-sm transition">
                        <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Novo Pedido
                    </a>
                @endif
                <a href="{{ route('calendario.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-[#031d14] border border-gray-300 dark:border-[#083d2c] hover:bg-gray-50 dark:hover:bg-[#063325] text-gray-700 dark:text-emerald-100 font-semibold rounded-lg shadow-sm text-sm transition">
                    <svg class="w-4 h-4 me-1.5 text-emerald-700 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Ver Calendário
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Cards Resumo -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white dark:bg-[#031d14] p-5 rounded-xl shadow-sm border border-gray-100 dark:border-[#083d2c] flex items-center justify-between transition-colors">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 dark:text-emerald-300/70 uppercase tracking-wider">Locações Ativas</p>
                        <h3 class="text-2xl font-black text-gray-900 dark:text-white mt-1">{{ $totalAtivos }}</h3>
                    </div>
                    <div class="p-3 bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 rounded-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                </div>

                <div class="bg-white dark:bg-[#031d14] p-5 rounded-xl shadow-sm border border-gray-100 dark:border-[#083d2c] flex items-center justify-between transition-colors">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 dark:text-emerald-300/70 uppercase tracking-wider">Montagens Hoje</p>
                        <h3 class="text-2xl font-black text-amber-600 dark:text-amber-400 mt-1">{{ $montagensHoje }}</h3>
                    </div>
                    <div class="p-3 bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 rounded-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>

                <div class="bg-white dark:bg-[#031d14] p-5 rounded-xl shadow-sm border border-gray-100 dark:border-[#083d2c] flex items-center justify-between transition-colors">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 dark:text-emerald-300/70 uppercase tracking-wider">Retiradas Hoje</p>
                        <h3 class="text-2xl font-black text-purple-600 dark:text-purple-400 mt-1">{{ $retiradasHoje }}</h3>
                    </div>
                    <div class="p-3 bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 rounded-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                    </div>
                </div>

                @if(Auth::user()->isAdmin())
                    <div class="bg-white dark:bg-[#031d14] p-5 rounded-xl shadow-sm border border-gray-100 dark:border-[#083d2c] flex items-center justify-between transition-colors">
                        <div>
                            <p class="text-xs font-semibold text-gray-500 dark:text-emerald-300/70 uppercase tracking-wider">Tendas no Estoque</p>
                            <h3 class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1">{{ $tendasDisponiveis }} disp.</h3>
                        </div>
                        <div class="p-3 bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 rounded-lg">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                            </svg>
                        </div>
                    </div>
                @else
                    <div class="bg-white dark:bg-[#031d14] p-5 rounded-xl shadow-sm border border-gray-100 dark:border-[#083d2c] flex items-center justify-between transition-colors">
                        <div>
                            <p class="text-xs font-semibold text-gray-500 dark:text-emerald-300/70 uppercase tracking-wider">Aguardando Retirada</p>
                            <h3 class="text-2xl font-black text-orange-600 dark:text-orange-400 mt-1">{{ $pedidosAguardandoRetirada }}</h3>
                        </div>
                        <div class="p-3 bg-orange-50 dark:bg-orange-950/60 text-orange-600 dark:text-orange-400 rounded-lg">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Tabela Próximas Instalações / Todas as Ordens -->
            <div class="bg-white dark:bg-[#031d14] rounded-xl shadow-sm border border-gray-100 dark:border-[#083d2c] overflow-hidden transition-colors">
                <div class="p-5 border-b border-gray-100 dark:border-[#083d2c] flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2.5">
                            <h3 class="font-bold text-lg text-gray-900 dark:text-white">
                                {{ Auth::user()->isMontador() ? 'Minhas Ordens e Montagens' : 'Ordens de Serviço e Locações' }}
                            </h3>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 dark:bg-emerald-950/70 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/40">
                                {{ $proximasInstalacoes->count() }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-emerald-300/70 mt-1">
                            {{ Auth::user()->isMontador() ? 'Acesse o pedido para instalar, anexar fotos ou devolver tendas a qualquer momento (inclusive em caso de antecipação).' : 'Acompanhe as montagens e devoluções programadas.' }}
                        </p>
                    </div>

                    <!-- Filtro Rápido: Todas as Ordens / Somente do Dia -->
                    <div class="flex items-center gap-2">
                        <div class="flex items-center p-1 bg-gray-100 dark:bg-[#021811] rounded-xl border border-gray-200 dark:border-emerald-800/50">
                            <a href="{{ route('dashboard', ['filtro' => 'todas']) }}" 
                               class="px-3 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1.5 {{ ($filtro ?? 'todas') === 'todas' ? 'bg-amber-400 text-slate-950 shadow-sm' : 'text-gray-600 dark:text-emerald-300 hover:text-gray-900 dark:hover:text-white' }}">
                                <span>Todas as Ordens</span>
                            </a>
                            <a href="{{ route('dashboard', ['filtro' => 'hoje']) }}" 
                               class="px-3 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1.5 {{ ($filtro ?? 'todas') === 'hoje' ? 'bg-amber-400 text-slate-950 shadow-sm' : 'text-gray-600 dark:text-emerald-300 hover:text-gray-900 dark:hover:text-white' }}">
                                <span class="w-2 h-2 rounded-full {{ ($filtro ?? 'todas') === 'hoje' ? 'bg-slate-950' : 'bg-emerald-400 animate-pulse' }}"></span>
                                <span>Somente do Dia</span>
                            </a>
                        </div>
                        <a href="{{ route('pedidos.index') }}" class="text-xs font-semibold text-emerald-600 dark:text-amber-400 hover:underline hidden sm:inline-block">Ver todos &rarr;</a>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-600 dark:text-emerald-100/90 min-w-[760px]">
                        <thead class="bg-gray-50 dark:bg-[#02140e] text-xs uppercase font-semibold text-gray-700 dark:text-emerald-300 border-b border-gray-200 dark:border-[#083d2c]">
                            <tr>
                                <th class="px-6 py-3">Pedido</th>
                                <th class="px-6 py-3">Cliente</th>
                                <th class="px-6 py-3">Data Montagem</th>
                                <th class="px-6 py-3">Previsão Retirada</th>
                                <th class="px-6 py-3">Endereço</th>
                                @if(Auth::user()->isAdmin())
                                    <th class="px-6 py-3">Montador</th>
                                @endif
                                <th class="px-6 py-3">Status</th>
                                <th class="px-6 py-3 text-right">Ação</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-[#083d2c]">
                            @forelse($proximasInstalacoes as $p)
                                <tr class="hover:bg-gray-50 dark:hover:bg-[#063325] transition">
                                    <td class="px-6 py-4 font-bold text-gray-900 dark:text-white">#{{ $p->id }}</td>
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-gray-900 dark:text-white">{{ $p->cliente->nome }}</div>
                                        <div class="text-xs text-gray-400 dark:text-emerald-400/60">{{ $p->cliente->telefone }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-semibold text-gray-900 dark:text-white flex items-center gap-1.5">
                                            <span>{{ $p->data_instalacao->format('d/m/Y') }}</span>
                                            @if($p->data_instalacao->isToday())
                                                <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-400 text-slate-950">Hoje</span>
                                            @endif
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-emerald-300/70">{{ $p->hora_instalacao ? substr($p->hora_instalacao, 0, 5) : 'A combinar' }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-xs font-medium text-gray-800 dark:text-emerald-200">
                                            {{ $p->data_retirada->format('d/m/Y') }}
                                        </div>
                                        <div class="text-[11px] text-gray-400 dark:text-emerald-400/60">{{ $p->hora_retirada ? substr($p->hora_retirada, 0, 5) : 'Horário livre' }}</div>
                                    </td>
                                    <td class="px-6 py-4 max-w-xs truncate" title="{{ $p->endereco_instalacao }}">
                                        {{ $p->endereco_instalacao }}
                                    </td>
                                    @if(Auth::user()->isAdmin())
                                        <td class="px-6 py-4">
                                            {{ $p->montador ? $p->montador->name : 'Não definido' }}
                                        </td>
                                    @endif
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $p->status_color }}">
                                            {{ $p->status_label }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('pedidos.show', $p) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold rounded-lg transition shadow-xs">
                                            <span>Abrir Pedido</span>
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ Auth::user()->isAdmin() ? '8' : '7' }}" class="px-6 py-8 text-center text-gray-500 dark:text-emerald-300/60">
                                        {{ ($filtro ?? 'todas') === 'hoje' ? 'Nenhuma ordem de montagem ou retirada agendada para hoje.' : 'Nenhuma ordem em andamento no momento.' }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
