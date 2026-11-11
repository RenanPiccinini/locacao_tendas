<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 dark:text-white leading-tight">
                    Propostas de Locação
                </h2>
                <p class="text-sm text-gray-500 dark:text-emerald-300/70">
                    Crie, gerencie e gere o PDF oficial de propostas comerciais
                </p>
            </div>

            <a href="{{ route('propostas.create') }}" class="inline-flex items-center px-4 py-2 bg-emerald-700 hover:bg-emerald-800 text-white font-semibold rounded-lg shadow-sm text-sm transition">
                <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Nova Proposta
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Filtros -->
            <div class="bg-white dark:bg-[#031d14] p-4 rounded-xl shadow-sm border border-gray-100 dark:border-[#083d2c] flex flex-col md:flex-row gap-4 items-center justify-between transition-colors">
                <form method="GET" action="{{ route('propostas.index') }}" class="flex flex-wrap items-center gap-3 w-full">
                    <input type="text"
                           name="busca"
                           value="{{ request('busca') }}"
                           placeholder="Buscar nº, identificação, cliente, CNPJ..."
                           class="border-gray-300 dark:border-emerald-700/60 dark:bg-[#021811] dark:text-white placeholder-gray-400 dark:placeholder-emerald-400/40 focus:border-amber-400 focus:ring-amber-400/20 rounded-lg text-sm px-3 py-2 w-full sm:w-64">

                    <select name="status" class="border-gray-300 dark:border-emerald-700/60 dark:bg-[#021811] dark:text-white focus:border-amber-400 focus:ring-amber-400/20 rounded-lg text-sm px-3 py-2">
                        <option value="">Todos os status</option>
                        <option value="rascunho" {{ request('status') === 'rascunho' ? 'selected' : '' }}>Rascunho</option>
                        <option value="enviada" {{ request('status') === 'enviada' ? 'selected' : '' }}>Enviada</option>
                        <option value="aprovada" {{ request('status') === 'aprovada' ? 'selected' : '' }}>Aprovada</option>
                        <option value="recusada" {{ request('status') === 'recusada' ? 'selected' : '' }}>Recusada</option>
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
                        <a href="{{ route('propostas.index') }}" class="text-sm text-gray-500 dark:text-amber-400 hover:underline">Limpar</a>
                    @endif
                </form>
            </div>

            <!-- Tabela de Propostas -->
            <div class="bg-white dark:bg-[#031d14] rounded-xl shadow-sm border border-gray-100 dark:border-[#083d2c] overflow-hidden transition-colors">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-600 dark:text-emerald-100/90 min-w-[820px]">
                        <thead class="bg-gray-50 dark:bg-[#02140e] text-xs uppercase font-semibold text-gray-700 dark:text-emerald-300 border-b border-gray-100 dark:border-[#083d2c]">
                            <tr>
                                <th class="px-6 py-4">Nº / Data</th>
                                <th class="px-6 py-4">Identificação</th>
                                <th class="px-6 py-4">Para (Destinatário)</th>
                                <th class="px-6 py-4">Data do Evento</th>
                                <th class="px-6 py-4">Total Geral</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-[#083d2c]">
                            @forelse($propostas as $proposta)
                                <tr class="hover:bg-gray-50/70 dark:hover:bg-[#042419] transition">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-bold text-gray-900 dark:text-white">Nº {{ $proposta->numero }}</div>
                                        <div class="text-xs text-gray-400 dark:text-emerald-300/70">
                                            {{ $proposta->data_proposta?->format('d/m/Y') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-semibold text-gray-900 dark:text-white">
                                            {{ $proposta->identificacao ?: '—' }}
                                        </div>
                                        <div class="text-xs text-gray-400 dark:text-emerald-400/70">
                                            {{ $proposta->itens->count() }} {{ $proposta->itens->count() === 1 ? 'item' : 'itens' }}
                                            @if($proposta->user)
                                                • por {{ $proposta->user->name }}
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-gray-900 dark:text-white">{{ $proposta->para_razao_social }}</div>
                                        @if($proposta->para_fantasia)
                                            <div class="text-xs text-gray-500 dark:text-emerald-200/70">{{ $proposta->para_fantasia }}</div>
                                        @endif
                                        @if($proposta->para_solicitante || $proposta->para_celular)
                                            <div class="text-xs text-emerald-700 dark:text-emerald-400 mt-0.5">
                                                {{ $proposta->para_solicitante }}
                                                @if($proposta->para_solicitante && $proposta->para_celular) • @endif
                                                {{ $proposta->para_celular }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-xs whitespace-nowrap">
                                        @if($proposta->data_inicio || $proposta->data_fim)
                                            <div>
                                                <span class="font-semibold text-gray-700 dark:text-emerald-300">De:</span>
                                                {{ $proposta->data_inicio?->format('d/m/Y') ?? '—' }}
                                            </div>
                                            <div>
                                                <span class="font-semibold text-gray-700 dark:text-emerald-300">Até:</span>
                                                {{ $proposta->data_fim?->format('d/m/Y') ?? '—' }}
                                            </div>
                                        @elseif($proposta->data_saida || $proposta->data_retorno)
                                            <div>
                                                <span class="font-semibold text-gray-700 dark:text-emerald-300">De:</span>
                                                {{ $proposta->data_saida?->format('d/m/Y') ?? '—' }}
                                            </div>
                                            <div>
                                                <span class="font-semibold text-gray-700 dark:text-emerald-300">Até:</span>
                                                {{ $proposta->data_retorno?->format('d/m/Y') ?? '—' }}
                                            </div>
                                        @else
                                            <span class="text-gray-400 dark:text-emerald-400/50">Não informado</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-extrabold text-emerald-700 dark:text-amber-400">
                                            R$ {{ number_format((float) $proposta->valor_total_geral, 2, ',', '.') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $proposta->status_color }}">
                                            {{ $proposta->status_label }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <a href="{{ route('propostas.pdf', $proposta) }}" target="_blank" class="p-1.5 text-amber-500 hover:text-amber-600 dark:hover:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-950/40 rounded transition" title="Visualizar PDF da Proposta">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                            </a>
                                            <a href="{{ route('propostas.download', $proposta) }}" class="p-1.5 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 rounded transition" title="Baixar PDF">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                            </a>
                                            <a href="{{ route('propostas.show', $proposta) }}" class="p-1.5 text-gray-500 dark:text-emerald-300 hover:text-emerald-600 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-[#063325] rounded transition" title="Ver Detalhes">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            </a>
                                            <a href="{{ route('propostas.edit', $proposta) }}" class="p-1.5 text-blue-500 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-950/40 rounded transition" title="Editar Proposta">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </a>
                                            <form action="{{ route('propostas.destroy', $proposta) }}" method="POST" data-confirm="Tem certeza que deseja excluir a Proposta <strong>Nº {{ $proposta->numero }}</strong> ({{ $proposta->para_razao_social }})?" data-confirm-title="Excluir Proposta" data-confirm-btn="Sim, Excluir" data-confirm-type="danger">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 text-red-500 hover:text-red-700 hover:bg-red-50 dark:hover:bg-red-950/40 rounded transition" title="Excluir Proposta">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-10 text-center text-gray-500 dark:text-emerald-300/60">
                                        Nenhuma proposta encontrada. Clique em <strong>Nova Proposta</strong> para criar a primeira.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($propostas->hasPages())
                    <div class="p-4 border-t border-gray-100 dark:border-[#083d2c]">
                        {{ $propostas->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
