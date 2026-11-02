<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 dark:text-white leading-tight">
                    Equipe de Montadores
                </h2>
                <p class="text-sm text-gray-500 dark:text-emerald-300/70">Cadastre e gerencie os montadores responsáveis pelas instalações</p>
            </div>
            <a href="{{ route('montadores.create') }}" class="inline-flex items-center px-4 py-2 bg-emerald-700 hover:bg-emerald-800 text-white font-semibold rounded-lg shadow-sm text-sm transition">
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
            <div class="bg-white dark:bg-[#031d14] p-4 rounded-xl shadow-xs border border-gray-200 dark:border-[#083d2c] flex flex-col md:flex-row gap-4 items-center justify-between transition-colors">
                <form method="GET" action="{{ route('montadores.index') }}" class="flex items-center gap-3 w-full md:w-auto">
                    <input type="text" name="busca" value="{{ request('busca') }}" placeholder="Buscar por nome, e-mail ou WhatsApp..." class="border-gray-300 dark:border-emerald-700/60 dark:bg-[#021811] dark:text-white placeholder-gray-400 dark:placeholder-emerald-400/40 focus:border-amber-400 focus:ring-amber-400/20 rounded-lg text-sm px-3 py-2 w-full sm:w-80">
                    <button type="submit" class="px-4 py-2 bg-emerald-800 hover:bg-emerald-700 dark:bg-emerald-700 dark:hover:bg-emerald-600 text-white text-sm font-semibold rounded-lg transition">
                        Buscar
                    </button>
                    @if(request('busca'))
                        <a href="{{ route('montadores.index') }}" class="text-sm text-gray-500 dark:text-amber-400 hover:underline">Limpar</a>
                    @endif
                </form>
            </div>

            <!-- Tabela de Montadores -->
            <div class="bg-white dark:bg-[#031d14] rounded-xl shadow-xs border border-gray-200 dark:border-[#083d2c] overflow-hidden transition-colors">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-600 dark:text-emerald-100/90 min-w-[640px]">
                    <thead class="bg-gray-50 dark:bg-[#02140e] text-xs uppercase font-semibold text-gray-700 dark:text-emerald-300 border-b border-gray-200 dark:border-[#083d2c]">
                        <tr>
                            <th class="px-6 py-4">Montador</th>
                            <th class="px-6 py-4">WhatsApp (Notificações)</th>
                            <th class="px-6 py-4">Serviços em Aberto</th>
                            <th class="px-6 py-4">Total Realizado</th>
                            <th class="px-6 py-4 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-[#083d2c]">
                        @forelse($montadores as $montador)
                            <tr class="hover:bg-gray-50 dark:hover:bg-[#063325] transition">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full bg-emerald-100 dark:bg-emerald-950/70 text-emerald-800 dark:text-emerald-300 border border-transparent dark:border-emerald-700/50 font-bold flex items-center justify-center text-xs shrink-0">
                                            {{ strtoupper(substr($montador->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-900 dark:text-white">{{ $montador->name }}</div>
                                            <div class="text-xs text-gray-400 dark:text-emerald-400/60">{{ $montador->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($montador->whatsapp)
                                        <span class="inline-flex items-center gap-1.5 font-semibold text-emerald-600 dark:text-amber-400">
                                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-5.805 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                                            {{ $montador->whatsapp }}
                                        </span>
                                    @else
                                        <span class="text-xs text-amber-500 font-medium">Não configurado</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $montador->pedidos_ativos_count > 0 ? 'bg-amber-100 dark:bg-amber-950/70 text-amber-800 dark:text-amber-300' : 'bg-gray-100 dark:bg-[#02140e] text-gray-600 dark:text-emerald-300' }}">
                                        {{ $montador->pedidos_ativos_count }} em andamento
                                    </span>
                                </td>
                                <td class="px-6 py-4 font-semibold text-gray-700 dark:text-emerald-200">
                                    {{ $montador->pedidos_concluidos_count }} concluídos
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <a href="{{ route('montadores.edit', $montador) }}" class="text-xs text-emerald-700 dark:text-amber-400 hover:underline font-semibold">Editar</a>
                                    
                                    <form action="{{ route('montadores.destroy', $montador) }}" method="POST" class="inline" onsubmit="return confirm('Deseja realmente remover este montador?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs text-red-600 dark:text-red-400 hover:underline font-semibold">Excluir</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500 dark:text-emerald-300/60">Nenhum montador cadastrado.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                </div>

                @if($montadores->hasPages())
                    <div class="p-4 border-t border-gray-100 dark:border-[#083d2c]">
                        {{ $montadores->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
