<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    Equipe de Montadores
                </h2>
                <p class="text-sm text-gray-500">Cadastre e gerencie os montadores responsáveis pelas instalações</p>
            </div>
            <a href="{{ route('montadores.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-sm text-sm transition">
                <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                + Novo Montador
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Busca -->
            <div class="bg-white p-4 rounded-xl shadow-xs border border-gray-200 flex flex-col md:flex-row gap-4 items-center justify-between">
                <form method="GET" action="{{ route('montadores.index') }}" class="flex items-center gap-3 w-full md:w-auto">
                    <input type="text" name="busca" value="{{ request('busca') }}" placeholder="Buscar por nome, e-mail ou WhatsApp..." class="border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-lg text-sm px-3 py-2 w-full sm:w-80">
                    <button type="submit" class="px-4 py-2 bg-gray-800 text-white text-sm font-semibold rounded-lg hover:bg-gray-900 transition">
                        Buscar
                    </button>
                    @if(request('busca'))
                        <a href="{{ route('montadores.index') }}" class="text-sm text-gray-500 hover:text-gray-700 underline">Limpar</a>
                    @endif
                </form>
            </div>

            <!-- Tabela de Montadores -->
            <div class="bg-white rounded-xl shadow-xs border border-gray-200 overflow-hidden">
                <table class="w-full text-left text-sm text-gray-600">
                    <thead class="bg-gray-50 text-xs uppercase font-semibold text-gray-700 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4">Montador</th>
                            <th class="px-6 py-4">WhatsApp (Notificações)</th>
                            <th class="px-6 py-4">Serviços em Aberto</th>
                            <th class="px-6 py-4">Total Realizado</th>
                            <th class="px-6 py-4 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($montadores as $montador)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full bg-blue-100 text-blue-700 font-bold flex items-center justify-center text-xs shrink-0">
                                            {{ strtoupper(substr($montador->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-900">{{ $montador->name }}</div>
                                            <div class="text-xs text-gray-400">{{ $montador->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-5.805 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                                        <span>{{ $montador->whatsapp ?? 'Não informado' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ $montador->pedidos_ativos > 0 ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-gray-600' }}">
                                        {{ $montador->pedidos_ativos }} em andamento
                                    </span>
                                </td>
                                <td class="px-6 py-4 font-semibold text-gray-700">
                                    {{ $montador->total_pedidos }} pedidos
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <a href="{{ route('montadores.edit', $montador) }}" class="text-xs text-blue-600 hover:text-blue-800 font-semibold">Editar</a>
                                    <form action="{{ route('montadores.destroy', $montador) }}" method="POST" class="inline" onsubmit="return confirm('Tem certeza que deseja remover este montador?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs text-red-600 hover:text-red-800 font-semibold">Excluir</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                    Nenhum montador cadastrado até o momento.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                @if($montadores->hasPages())
                    <div class="p-4 border-t border-gray-100">
                        {{ $montadores->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
