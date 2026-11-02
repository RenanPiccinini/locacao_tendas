<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    Estoque de Tendas & Disponibilidade
                </h2>
                <p class="text-sm text-gray-500">Consulte o que está disponível e em locação por data ou período</p>
            </div>
            <a href="{{ route('tendas.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-sm text-sm transition">
                + Nova Tenda
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Filtro de Data Inicial e Final -->
            <div class="bg-white p-5 rounded-xl shadow-xs border border-gray-200">
                <form method="GET" action="{{ route('tendas.index') }}" class="space-y-4">
                    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-4">
                        <div class="flex flex-wrap items-end gap-3 sm:gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                                    Data Inicial *
                                </label>
                                <input type="date" 
                                       id="filtro_data_inicio"
                                       name="data_inicio" 
                                       value="{{ $dadosDisponibilidade['data_inicio'] }}" 
                                       required 
                                       class="border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-lg text-sm px-3 py-2 w-full sm:w-44 font-medium">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                                    Data Final
                                </label>
                                <input type="date" 
                                       id="filtro_data_fim"
                                       name="data_fim" 
                                       value="{{ $dadosDisponibilidade['data_fim'] }}" 
                                       class="border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-lg text-sm px-3 py-2 w-full sm:w-44 font-medium">
                            </div>

                            <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg text-sm shadow-sm transition flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                </svg>
                                Filtrar Disponibilidade
                            </button>
                        </div>

                        <!-- Atalhos Rápidos -->
                        <div class="flex flex-wrap items-center gap-2 text-xs">
                            <span class="text-gray-400 font-semibold mr-1">Atalhos:</span>
                            <a href="{{ route('tendas.index', ['data_inicio' => date('Y-m-d'), 'data_fim' => date('Y-m-d')]) }}" 
                               class="px-2.5 py-1.5 rounded-lg border border-gray-200 hover:bg-gray-50 text-gray-700 font-medium transition {{ $dadosDisponibilidade['data_inicio'] === date('Y-m-d') && $dadosDisponibilidade['data_fim'] === date('Y-m-d') ? 'bg-blue-50 border-blue-300 text-blue-700 font-bold' : '' }}">
                                Hoje
                            </a>
                            <a href="{{ route('tendas.index', ['data_inicio' => date('Y-m-d', strtotime('+1 day')), 'data_fim' => date('Y-m-d', strtotime('+1 day'))]) }}" 
                               class="px-2.5 py-1.5 rounded-lg border border-gray-200 hover:bg-gray-50 text-gray-700 font-medium transition">
                                Amanhã
                            </a>
                            <a href="{{ route('tendas.index', ['data_inicio' => date('Y-m-d', strtotime('next saturday')), 'data_fim' => date('Y-m-d', strtotime('next sunday'))]) }}" 
                               class="px-2.5 py-1.5 rounded-lg border border-gray-200 hover:bg-gray-50 text-gray-700 font-medium transition">
                                Próximo Fim de Semana
                            </a>
                        </div>
                    </div>
                </form>

                <div class="mt-3 pt-3 border-t border-gray-100 flex flex-wrap items-center justify-between text-xs text-gray-500">
                    <div class="flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                        <span>Exibindo disponibilidade para: <strong class="text-gray-900 font-bold">{{ $dadosDisponibilidade['data_formatada'] }}</strong></span>
                    </div>
                    <span class="text-gray-400">Dica: Selecione apenas a Data Inicial ou coloque as duas iguais para verificar um dia específico.</span>
                </div>
            </div>

            <!-- Cards de Resumo da Data Selecionada -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-xs">
                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Estoque Total Físico</span>
                    <div class="text-2xl font-bold text-gray-900 mt-1">{{ $dadosDisponibilidade['total_tendas_estoque'] }} <span class="text-sm font-normal text-gray-500">unidades</span></div>
                    <p class="text-[11px] text-gray-400 mt-1">Patrimônio total da empresa</p>
                </div>

                <div class="bg-white p-5 rounded-xl border border-emerald-100 shadow-xs bg-emerald-50/20">
                    <span class="text-xs font-bold text-emerald-700 uppercase tracking-wider">Disponível em {{ $dadosDisponibilidade['is_mesmo_dia'] ? $dadosDisponibilidade['data_formatada'] : 'no Período' }}</span>
                    <div class="text-2xl font-bold text-emerald-600 mt-1">{{ $dadosDisponibilidade['total_disponivel'] }} <span class="text-sm font-normal text-emerald-600">unidades</span></div>
                    <p class="text-[11px] text-emerald-600/80 mt-1">Livres para novas locações</p>
                </div>

                <div class="bg-white p-5 rounded-xl border border-blue-100 shadow-xs bg-blue-50/20">
                    <span class="text-xs font-bold text-blue-700 uppercase tracking-wider">Em Locação em {{ $dadosDisponibilidade['is_mesmo_dia'] ? $dadosDisponibilidade['data_formatada'] : 'no Período' }}</span>
                    <div class="text-2xl font-bold text-blue-600 mt-1">{{ $dadosDisponibilidade['total_em_locacao'] }} <span class="text-sm font-normal text-blue-600">unidades</span></div>
                    <p class="text-[11px] text-blue-600/80 mt-1">Montadas ou em transporte</p>
                </div>

                <div class="bg-white p-5 rounded-xl border border-purple-100 shadow-xs bg-purple-50/20">
                    <span class="text-xs font-bold text-purple-700 uppercase tracking-wider">Pedidos Ativos</span>
                    <div class="text-2xl font-bold text-purple-600 mt-1">{{ $dadosDisponibilidade['total_pedidos'] }} <span class="text-sm font-normal text-purple-600">pedidos</span></div>
                    <p class="text-[11px] text-purple-600/80 mt-1">Atendimentos no período</p>
                </div>
            </div>

            <!-- Tabela 1: Modelos de Tendas e Estoque na Data -->
            <div class="bg-white rounded-xl shadow-xs border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <div>
                        <h3 class="font-bold text-gray-900 text-base">Disponibilidade por Modelo de Tenda</h3>
                        <p class="text-xs text-gray-500">Quantidades calculadas para <strong>{{ $dadosDisponibilidade['data_formatada'] }}</strong></p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-600 min-w-[700px]">
                        <thead class="bg-gray-50 text-xs uppercase font-semibold text-gray-700 border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-4">Tenda</th>
                                <th class="px-6 py-4">Dimensões</th>
                                <th class="px-6 py-4">Total Físico</th>
                                <th class="px-6 py-4">Disponível no Dia</th>
                                <th class="px-6 py-4">Em Locação no Dia</th>
                                <th class="px-6 py-4">Valor Diária</th>
                                <th class="px-6 py-4 text-right">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($tendas as $tenda)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-gray-900">{{ $tenda->nome }}</div>
                                        <div class="text-xs text-gray-400">{{ $tenda->descricao }}</div>
                                    </td>
                                    <td class="px-6 py-4 font-semibold text-gray-800">{{ $tenda->tamanho }}</td>
                                    <td class="px-6 py-4 font-medium text-gray-700">{{ $tenda->quantidade_total }} un.</td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold {{ $tenda->quantidade_disponivel_periodo > 0 ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                                            <span class="w-2 h-2 rounded-full {{ $tenda->quantidade_disponivel_periodo > 0 ? 'bg-emerald-500' : 'bg-red-500' }} me-1.5"></span>
                                            {{ $tenda->quantidade_disponivel_periodo }} disponíveis
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($tenda->quantidade_em_locacao > 0)
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800">
                                                {{ $tenda->quantidade_em_locacao }} em locação
                                            </span>
                                        @else
                                            <span class="text-xs text-gray-400 font-medium">0 em locação</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 font-semibold text-gray-900">
                                        R$ {{ number_format($tenda->valor_diaria, 2, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 text-right space-x-2">
                                        <a href="{{ route('tendas.edit', $tenda) }}" class="text-xs text-blue-600 hover:text-blue-800 font-semibold">Editar</a>
                                        <form action="{{ route('tendas.destroy', $tenda) }}" method="POST" class="inline" onsubmit="return confirm('Excluir esta tenda do estoque?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs text-red-600 hover:text-red-800 font-semibold">Excluir</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">Nenhuma tenda cadastrada no sistema.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tabela 2: O que estará em locação e QUANDO RETORNA -->
            <div class="bg-white rounded-xl shadow-xs border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-purple-600 inline-block"></span>
                            <h3 class="font-bold text-gray-900 text-base">Tendas em Locação e Previsão de Retorno</h3>
                        </div>
                        <p class="text-xs text-gray-500">Relação de tendas comprometidas em <strong>{{ $dadosDisponibilidade['data_formatada'] }}</strong> e quando retornam ao estoque</p>
                    </div>
                    <span class="text-xs font-bold px-2.5 py-1 bg-purple-100 text-purple-800 rounded-full">
                        {{ $dadosDisponibilidade['locacoes']->count() }} {{ $dadosDisponibilidade['locacoes']->count() === 1 ? 'item alugado' : 'itens alugados' }}
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-600 min-w-[760px]">
                        <thead class="bg-gray-50 text-xs uppercase font-semibold text-gray-700 border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-4">Tenda Alugada</th>
                                <th class="px-6 py-4">Pedido / Cliente</th>
                                <th class="px-6 py-4">Data Instalação</th>
                                <th class="px-6 py-4">Quando Retorna (Retirada)</th>
                                <th class="px-6 py-4">Montador Responsável</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">Ação</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($dadosDisponibilidade['locacoes'] as $locacao)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-gray-900">
                                            {{ $locacao['quantidade'] }}x {{ $locacao['tenda_nome'] }}
                                        </div>
                                        <div class="text-xs text-gray-500">Tamanho: {{ $locacao['tenda_tamanho'] }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-gray-900">#{{ $locacao['pedido_id'] }} - {{ $locacao['cliente_nome'] }}</div>
                                        <div class="text-xs text-gray-400">{{ $locacao['cliente_telefone'] }}</div>
                                        @if($locacao['cidade'])
                                            <div class="text-[11px] text-gray-500">{{ $locacao['cidade'] }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-xs font-medium text-gray-700">
                                        <div>{{ $locacao['data_instalacao']->format('d/m/Y') }}</div>
                                        <div class="text-gray-400">{{ $locacao['hora_instalacao'] ? substr($locacao['hora_instalacao'], 0, 5) : 'Horário livre' }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="inline-flex flex-col">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-purple-100 text-purple-900 border border-purple-200">
                                                <svg class="w-3.5 h-3.5 me-1 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                {{ $locacao['data_retirada']->format('d/m/Y') }}
                                            </span>
                                            <span class="text-[11px] text-purple-700 font-semibold mt-1">
                                                às {{ $locacao['hora_retirada'] ? substr($locacao['hora_retirada'], 0, 5) : 'Horário a combinar' }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-xs">
                                        <div class="font-semibold text-gray-800">{{ $locacao['montador_nome'] }}</div>
                                        @if($locacao['montador_whatsapp'])
                                            <div class="text-emerald-600 font-medium">{{ $locacao['montador_whatsapp'] }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $locacao['status_color'] }}">
                                            {{ $locacao['status_label'] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('pedidos.show', $locacao['pedido_id']) }}" class="inline-flex items-center px-3 py-1 bg-blue-50 text-blue-700 hover:bg-blue-100 font-semibold text-xs rounded transition">
                                            Ver Pedido &rarr;
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-10 text-center">
                                        <div class="max-w-md mx-auto">
                                            <div class="w-12 h-12 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto mb-2">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            </div>
                                            <div class="font-bold text-gray-800 text-sm">Nenhuma tenda em locação nesta data!</div>
                                            <div class="text-xs text-gray-500 mt-0.5">
                                                Todo o estoque de tendas está 100% disponível para atender novos clientes em {{ $dadosDisponibilidade['data_formatada'] }}.
                                            </div>
                                        </div>
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
