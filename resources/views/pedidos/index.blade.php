<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 dark:text-white leading-tight">
                    Pedidos e Ordens de Serviço
                </h2>
                <p class="text-sm text-gray-500 dark:text-emerald-300/70">Gerencie locações, gere ordens de serviço e acompanhe montagens</p>
            </div>

            @if(Auth::user()->isAdmin())
                <a href="{{ route('pedidos.create') }}" class="inline-flex items-center px-4 py-2 bg-emerald-700 hover:bg-emerald-800 text-white font-semibold rounded-lg shadow-sm text-sm transition">
                    <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Novo Pedido de Locação
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Atalhos de Visualização Rápida (Todas as Ordens x Somente do Dia) -->
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <a href="{{ route('pedidos.index', array_merge(request()->except('filtro_dia', 'page'), ['filtro_dia' => 'todos'])) }}" 
                       class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1.5 {{ request('filtro_dia', 'todos') === 'todos' ? 'bg-amber-400 text-slate-950 shadow-sm' : 'bg-white dark:bg-[#031d14] border border-gray-200 dark:border-emerald-800/40 text-gray-700 dark:text-emerald-200 hover:bg-gray-50 dark:hover:bg-[#063325]' }}">
                        <span>Todas as Ordens / Pedidos</span>
                    </a>
                    <a href="{{ route('pedidos.index', array_merge(request()->except('filtro_dia', 'page'), ['filtro_dia' => 'hoje'])) }}" 
                       class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1.5 {{ request('filtro_dia') === 'hoje' ? 'bg-amber-400 text-slate-950 shadow-sm' : 'bg-white dark:bg-[#031d14] border border-gray-200 dark:border-emerald-800/40 text-gray-700 dark:text-emerald-200 hover:bg-gray-50 dark:hover:bg-[#063325]' }}">
                        <span class="w-2 h-2 rounded-full {{ request('filtro_dia') === 'hoje' ? 'bg-slate-950' : 'bg-emerald-400 animate-pulse' }}"></span>
                        <span>Somente do Dia (Hoje)</span>
                    </a>
                </div>

                @if(Auth::user()->isMontador())
                    <p class="text-xs text-gray-500 dark:text-emerald-300/70">
                        ⚡ Todas as suas ordens estão liberadas para operação (instalação ou devolução antecipada).
                    </p>
                @endif
            </div>

            <!-- Filtros -->
            <div class="bg-white dark:bg-[#031d14] p-4 rounded-xl shadow-sm border border-gray-100 dark:border-[#083d2c] flex flex-col md:flex-row gap-4 items-center justify-between transition-colors">
                <form method="GET" action="{{ route('pedidos.index') }}" class="flex flex-wrap items-center gap-3 w-full">
                    <input type="text" name="busca" value="{{ request('busca') }}" placeholder="Buscar por cliente ou telefone..." class="border-gray-300 dark:border-emerald-700/60 dark:bg-[#021811] dark:text-white placeholder-gray-400 dark:placeholder-emerald-400/40 focus:border-amber-400 focus:ring-amber-400/20 rounded-lg text-sm px-3 py-2 w-full sm:w-56">

                    <select name="status" class="border-gray-300 dark:border-emerald-700/60 dark:bg-[#021811] dark:text-white focus:border-amber-400 focus:ring-amber-400/20 rounded-lg text-sm px-3 py-2">
                        <option value="">Todos os status</option>
                        <option value="pendente" {{ request('status') === 'pendente' ? 'selected' : '' }}>Pendente</option>
                        <option value="os_gerada" {{ request('status') === 'os_gerada' ? 'selected' : '' }}>O.S. Gerada</option>
                        <option value="em_instalacao" {{ request('status') === 'em_instalacao' ? 'selected' : '' }}>Em Instalação</option>
                        <option value="instalado" {{ request('status') === 'instalado' ? 'selected' : '' }}>Instalado</option>
                        <option value="aguardando_retirada" {{ request('status') === 'aguardando_retirada' ? 'selected' : '' }}>Aguardando Retirada</option>
                        <option value="em_retirada" {{ request('status') === 'em_retirada' ? 'selected' : '' }}>Em Retirada</option>
                        <option value="concluido" {{ request('status') === 'concluido' ? 'selected' : '' }}>Concluído</option>
                    </select>

                    <div class="flex items-center gap-1.5">
                        <span class="text-xs font-semibold text-gray-500 dark:text-emerald-300/70">De:</span>
                        <input type="date" name="data_inicio" value="{{ request('data_inicio') }}" class="border-gray-300 dark:border-emerald-700/60 dark:bg-[#021811] dark:text-white focus:border-amber-400 focus:ring-amber-400/20 rounded-lg text-sm px-3 py-2">
                    </div>

                    <div class="flex items-center gap-1.5">
                        <span class="text-xs font-semibold text-gray-500 dark:text-emerald-300/70">Até:</span>
                        <input type="date" name="data_fim" value="{{ request('data_fim') }}" class="border-gray-300 dark:border-emerald-700/60 dark:bg-[#021811] dark:text-white focus:border-amber-400 focus:ring-amber-400/20 rounded-lg text-sm px-3 py-2">
                    </div>

                    <button type="submit" class="px-4 py-2 bg-emerald-800 hover:bg-emerald-700 dark:bg-emerald-700 dark:hover:bg-emerald-600 text-white text-sm font-semibold rounded-lg transition flex items-center gap-1.5 shadow-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                        Filtrar
                    </button>

                    @if(request()->hasAny(['busca', 'status', 'data_inicio', 'data_fim']))
                        <a href="{{ route('pedidos.index') }}" class="text-sm text-gray-500 dark:text-amber-400 hover:underline">Limpar</a>
                    @endif
                </form>
            </div>

            @if($dadosDisponibilidade)
                <!-- Painel de Disponibilidade da Data Filtrada -->
                <div class="bg-gradient-to-r from-emerald-50 to-teal-50 dark:from-[#02281e] dark:to-[#033628] border border-emerald-200 dark:border-emerald-700/60 p-4 rounded-xl shadow-xs transition-colors">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-emerald-600 dark:bg-emerald-700 text-white flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900 dark:text-white text-sm">Disponibilidade do Estoque para: {{ $dadosDisponibilidade['data_formatada'] }}</h4>
                                <div class="flex flex-wrap items-center gap-3 text-xs mt-0.5">
                                    <span class="text-emerald-700 dark:text-emerald-300 font-bold bg-emerald-100 dark:bg-emerald-950/70 px-2 py-0.5 rounded border border-transparent dark:border-emerald-700/50">{{ $dadosDisponibilidade['total_disponivel'] }} tendas disponíveis</span>
                                    <span class="text-amber-700 dark:text-amber-300 font-bold bg-amber-100 dark:bg-amber-950/70 px-2 py-0.5 rounded border border-transparent dark:border-amber-700/50">{{ $dadosDisponibilidade['total_em_locacao'] }} tendas em locação</span>
                                    <span class="text-purple-700 dark:text-purple-300 font-bold bg-purple-100 dark:bg-purple-950/70 px-2 py-0.5 rounded border border-transparent dark:border-purple-700/50">{{ $dadosDisponibilidade['total_pedidos'] }} pedidos ativos</span>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('tendas.index', ['data_inicio' => request('data_inicio'), 'data_fim' => request('data_fim')]) }}" class="px-3.5 py-1.5 bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs rounded-lg transition shadow-xs flex items-center gap-1.5 w-fit">
                            Ver Detalhamento do Estoque &rarr;
                        </a>
                    </div>
                </div>
            @endif

            <!-- Tabela de Pedidos -->
            <div class="bg-white dark:bg-[#031d14] rounded-xl shadow-sm border border-gray-100 dark:border-[#083d2c] overflow-hidden transition-colors">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-600 dark:text-emerald-100/90 min-w-[760px]">
                        <thead class="bg-gray-50 dark:bg-[#02140e] text-xs uppercase font-semibold text-gray-700 dark:text-emerald-300 border-b border-gray-100 dark:border-[#083d2c]">
                            <tr>
                                <th class="px-6 py-4">ID</th>
                                <th class="px-6 py-4">Cliente</th>
                                <th class="px-6 py-4">Datas (Instalação / Retirada)</th>
                                <th class="px-6 py-4">Tendas</th>
                                <th class="px-6 py-4">Montador</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-[#083d2c]">
                            @forelse($pedidos as $pedido)
                                <tr class="hover:bg-gray-50 dark:hover:bg-[#063325] transition">
                                    <td class="px-6 py-4 font-bold text-gray-900 dark:text-white">
                                        #{{ $pedido->id }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-gray-900 dark:text-white">{{ $pedido->cliente->nome }}</div>
                                        <div class="text-xs text-gray-400 dark:text-emerald-400/60">{{ $pedido->cliente->telefone }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-xs">
                                            <span class="font-semibold text-emerald-700 dark:text-emerald-400">Instalação:</span> {{ $pedido->data_instalacao->format('d/m/Y') }} {{ $pedido->hora_instalacao ? substr($pedido->hora_instalacao, 0, 5) : '' }}
                                        </div>
                                        <div class="text-xs text-purple-700 dark:text-purple-400 mt-0.5">
                                            <span class="font-semibold">Retirada:</span> {{ $pedido->data_retirada->format('d/m/Y') }} {{ $pedido->hora_retirada ? substr($pedido->hora_retirada, 0, 5) : '' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-xs space-y-0.5">
                                            @foreach($pedido->tendas as $t)
                                                <span class="inline-block bg-gray-100 dark:bg-[#021811] text-gray-800 dark:text-emerald-200 border border-transparent dark:border-emerald-700/40 px-2 py-0.5 rounded text-xs">
                                                    {{ $t->pivot->quantidade }}x {{ $t->nome }} ({{ $t->tamanho }})
                                                </span>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($pedido->montador)
                                            <div class="font-medium text-gray-900 dark:text-white">{{ $pedido->montador->name }}</div>
                                            <div class="text-xs text-emerald-600 dark:text-emerald-400 flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-5.805 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                                                {{ $pedido->montador->whatsapp ?? 'Sem WPP' }}
                                            </div>
                                        @else
                                            <span class="text-xs text-amber-600 dark:text-amber-400 font-semibold">Não definido</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $pedido->status_color }}">
                                            {{ $pedido->status_label }}
                                        </span>
                                        @if($pedido->ordensServico->isNotEmpty())
                                            <div class="flex flex-wrap gap-1 mt-1.5">
                                                @foreach($pedido->ordensServico as $osItem)
                                                    <a href="{{ route('os.pdf', $osItem) }}" target="_blank" title="{{ $osItem->tipo_label }} (Status: {{ $osItem->status_label }})" class="text-[10px] px-1.5 py-0.5 rounded font-mono font-bold bg-gray-100 dark:bg-[#021811] text-gray-700 dark:text-emerald-300 hover:text-emerald-600 dark:hover:text-amber-400 border border-gray-200 dark:border-emerald-700/40 transition">
                                                        {{ $osItem->numero_os }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('pedidos.contrato.ver', $pedido) }}" target="_blank" class="p-1.5 text-amber-500 hover:text-amber-600 dark:hover:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-950/40 rounded transition" title="Ver Contrato do Pedido #{{ $pedido->id }}">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            </a>
                                            <a href="{{ route('pedidos.show', $pedido) }}" class="p-1.5 text-gray-500 dark:text-emerald-300 hover:text-emerald-600 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-[#063325] rounded transition" title="Ver Detalhes">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            </a>
                                            @if(Auth::user()->isAdmin())
                                                <form action="{{ route('pedidos.destroy', $pedido) }}" method="POST" data-confirm="Tem certeza que deseja cancelar/excluir o pedido <strong>#{{ $pedido->id }}</strong>?<br><span class='text-xs text-amber-500 font-medium'>As tendas locadas voltarão automaticamente ao estoque disponível.</span>" data-confirm-title="Cancelar Pedido" data-confirm-btn="Sim, Cancelar Pedido" data-confirm-type="danger">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="p-1.5 text-red-500 hover:text-red-700 hover:bg-red-50 dark:hover:bg-red-950/40 rounded transition" title="Excluir">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-8 text-center text-gray-500 dark:text-emerald-300/60">
                                        Nenhum pedido encontrado.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($pedidos->hasPages())
                    <div class="p-4 border-t border-gray-100 dark:border-[#083d2c]">
                        {{ $pedidos->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
