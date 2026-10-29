<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    Pedidos e Ordens de Serviço
                </h2>
                <p class="text-sm text-gray-500">Gerencie locações, gere ordens de serviço e acompanhe montagens</p>
            </div>

            @if(Auth::user()->isAdmin())
                <a href="{{ route('pedidos.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-sm text-sm transition">
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

            <!-- Filtros -->
            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex flex-col md:flex-row gap-4 items-center justify-between">
                <form method="GET" action="{{ route('pedidos.index') }}" class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                    <input type="text" name="busca" value="{{ request('busca') }}" placeholder="Buscar por cliente ou telefone..." class="border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-lg text-sm px-3 py-2 w-full sm:w-64">

                    <select name="status" class="border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-lg text-sm px-3 py-2">
                        <option value="">Todos os status</option>
                        <option value="pendente" {{ request('status') === 'pendente' ? 'selected' : '' }}>Pendente</option>
                        <option value="os_gerada" {{ request('status') === 'os_gerada' ? 'selected' : '' }}>O.S. Gerada</option>
                        <option value="em_instalacao" {{ request('status') === 'em_instalacao' ? 'selected' : '' }}>Em Instalação</option>
                        <option value="instalado" {{ request('status') === 'instalado' ? 'selected' : '' }}>Instalado</option>
                        <option value="aguardando_retirada" {{ request('status') === 'aguardando_retirada' ? 'selected' : '' }}>Aguardando Retirada</option>
                        <option value="em_retirada" {{ request('status') === 'em_retirada' ? 'selected' : '' }}>Em Retirada</option>
                        <option value="concluido" {{ request('status') === 'concluido' ? 'selected' : '' }}>Concluído</option>
                    </select>

                    <button type="submit" class="px-4 py-2 bg-gray-800 text-white text-sm font-semibold rounded-lg hover:bg-gray-900 transition">
                        Filtrar
                    </button>

                    @if(request()->hasAny(['busca', 'status']))
                        <a href="{{ route('pedidos.index') }}" class="text-sm text-gray-500 hover:text-gray-700 underline">Limpar</a>
                    @endif
                </form>
            </div>

            <!-- Tabela de Pedidos -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-600 min-w-[760px]">
                        <thead class="bg-gray-50 text-xs uppercase font-semibold text-gray-700 border-b border-gray-100">
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
                        <tbody class="divide-y divide-gray-100">
                            @forelse($pedidos as $pedido)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 font-bold text-gray-900">
                                        #{{ $pedido->id }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-gray-900">{{ $pedido->cliente->nome }}</div>
                                        <div class="text-xs text-gray-400">{{ $pedido->cliente->telefone }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-xs">
                                            <span class="font-semibold text-blue-700">Instalação:</span> {{ $pedido->data_instalacao->format('d/m/Y') }} {{ $pedido->hora_instalacao ? substr($pedido->hora_instalacao, 0, 5) : '' }}
                                        </div>
                                        <div class="text-xs text-purple-700 mt-0.5">
                                            <span class="font-semibold">Retirada:</span> {{ $pedido->data_retirada->format('d/m/Y') }} {{ $pedido->hora_retirada ? substr($pedido->hora_retirada, 0, 5) : '' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-xs space-y-0.5">
                                            @foreach($pedido->tendas as $t)
                                                <span class="inline-block bg-gray-100 text-gray-800 px-2 py-0.5 rounded text-xs">
                                                    {{ $t->pivot->quantidade }}x {{ $t->nome }} ({{ $t->tamanho }})
                                                </span>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($pedido->montador)
                                            <div class="font-medium text-gray-900">{{ $pedido->montador->name }}</div>
                                            <div class="text-xs text-emerald-600 flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-5.805 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                                                {{ $pedido->montador->whatsapp ?? 'Sem WPP' }}
                                            </div>
                                        @else
                                            <span class="text-xs text-amber-600 font-semibold">Não definido</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $pedido->status_color }}">
                                            {{ $pedido->status_label }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right space-x-2">
                                        <a href="{{ route('pedidos.show', $pedido) }}" class="inline-flex items-center px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded transition shadow-sm">
                                            Acessar
                                        </a>

                                        @if(Auth::user()->isAdmin() && $pedido->status === 'pendente')
                                            <form action="{{ route('pedidos.gerar-os', $pedido) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center px-3 py-1 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded transition shadow-sm" title="Gera O.S. e notifica montador no WhatsApp">
                                                    Gerar O.S.
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                        Nenhum pedido encontrado.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($pedidos->hasPages())
                    <div class="p-4 border-t border-gray-100">
                        {{ $pedidos->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>

