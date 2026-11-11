<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <h2 class="font-bold text-2xl text-gray-800 dark:text-white leading-tight">
                        Proposta de Locação Nº {{ $proposta->numero }}
                    </h2>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold {{ $proposta->status_color }}">
                        {{ $proposta->status_label }}
                    </span>
                </div>
                <p class="text-sm text-gray-500 dark:text-emerald-300/70 mt-1">
                    Para: <strong class="text-gray-900 dark:text-white">{{ $proposta->para_razao_social }}</strong>
                    @if($proposta->identificacao)
                        | Identificação: <strong class="text-gray-900 dark:text-white">{{ $proposta->identificacao }}</strong>
                    @endif
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('propostas.index') }}" class="px-3.5 py-1.5 bg-gray-100 dark:bg-[#063325] hover:bg-gray-200 dark:hover:bg-[#0a4835] text-gray-700 dark:text-emerald-100 text-xs font-semibold rounded-lg transition border border-transparent dark:border-emerald-700/40">
                    &larr; Voltar
                </a>

                <a href="{{ route('propostas.pdf', $proposta) }}" target="_blank" class="px-4 py-1.5 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-slate-950 font-bold text-xs rounded-lg shadow-xs transition flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-slate-950" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    <span>Visualizar PDF</span>
                </a>

                <a href="{{ route('propostas.download', $proposta) }}" class="px-4 py-1.5 bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs rounded-lg shadow-xs transition flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    <span>Baixar PDF</span>
                </a>

                <a href="{{ route('propostas.edit', $proposta) }}" class="px-3.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-lg shadow-xs transition flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                    <span>Editar</span>
                </a>

                <form action="{{ route('propostas.destroy', $proposta) }}" method="POST" data-confirm="Tem certeza que deseja excluir a Proposta <strong>Nº {{ $proposta->numero }}</strong>?" data-confirm-title="Excluir Proposta" data-confirm-btn="Sim, Excluir" data-confirm-type="danger">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-3.5 py-1.5 bg-red-600 hover:bg-red-700 text-white font-bold text-xs rounded-lg shadow-xs transition flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        <span>Excluir</span>
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    @php
        $dataExtenso = $proposta->data_proposta
            ? \Carbon\Carbon::parse($proposta->data_proposta)->locale('pt_BR')->translatedFormat('d \d\e F \d\e Y')
            : '';

        $fmtPeriodo = fn ($d, $h) => $d
            ? \Carbon\Carbon::parse($d)->format('d/m/Y') . ($h ? ' ' . substr($h, 0, 5) : '')
            : '—';
    @endphp

    <div class="py-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Cabeçalho Resumo -->
            <div class="bg-white dark:bg-[#031d14] p-6 rounded-xl shadow-sm border border-gray-100 dark:border-[#083d2c] space-y-3">
                <div class="flex flex-wrap items-center justify-between gap-2 border-b border-gray-100 dark:border-[#083d2c] pb-3">
                    <div class="text-base font-extrabold text-gray-900 dark:text-white">
                        PROPOSTA DE LOCAÇÃO Nº {{ $proposta->numero }}
                    </div>
                    <div class="text-xs text-gray-500 dark:text-emerald-300/70">
                        {{ $dataExtenso }}
                        @if($proposta->user)
                            • Criada por {{ $proposta->user->name }}
                        @endif
                    </div>
                </div>

                <div>
                    <span class="text-xs font-bold uppercase text-gray-500 dark:text-emerald-300/70">Identificação:</span>
                    <span class="text-sm font-semibold text-gray-900 dark:text-white ml-1">{{ $proposta->identificacao ?: 'Não informada' }}</span>
                </div>
            </div>

            <!-- Cards De / Para -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- De: -->
                <div class="bg-white dark:bg-[#031d14] p-6 rounded-xl shadow-sm border border-gray-100 dark:border-[#083d2c] space-y-2 text-sm">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-amber-500 border-b border-gray-100 dark:border-[#083d2c] pb-2 mb-3">
                        De: (Locadora / Consultor)
                    </h3>
                    <div><strong class="text-gray-700 dark:text-emerald-300">Razão Social:</strong> {{ $proposta->de_razao_social }}</div>
                    <div><strong class="text-gray-700 dark:text-emerald-300">Fantasia:</strong> {{ $proposta->de_fantasia ?: '—' }}</div>
                    <div>
                        <strong class="text-gray-700 dark:text-emerald-300">CNPJ:</strong> {{ $proposta->de_cnpj ?: '—' }}
                        | <strong class="text-gray-700 dark:text-emerald-300">IE:</strong> {{ $proposta->de_ie ?: '—' }}
                        | <strong class="text-gray-700 dark:text-emerald-300">IM:</strong> {{ $proposta->de_im ?: '—' }}
                    </div>
                    <div>
                        <strong class="text-gray-700 dark:text-emerald-300">Endereço:</strong>
                        {{ $proposta->de_endereco ?: '—' }}{{ $proposta->de_bairro ? " - {$proposta->de_bairro}" : '' }}{{ $proposta->de_cidade ? " - {$proposta->de_cidade}" : '' }}{{ $proposta->de_uf ? "/{$proposta->de_uf}" : '' }}{{ $proposta->de_cep ? " (CEP: {$proposta->de_cep})" : '' }}
                    </div>
                    <div class="pt-1 border-t border-gray-100 dark:border-[#083d2c]">
                        <strong class="text-gray-700 dark:text-emerald-300">Consultor:</strong> {{ $proposta->de_consultor ?: '—' }}
                        | <strong class="text-gray-700 dark:text-emerald-300">Celular:</strong> {{ $proposta->de_celular ?: '—' }}
                        | <strong class="text-gray-700 dark:text-emerald-300">Email:</strong> {{ $proposta->de_email ?: '—' }}
                    </div>
                </div>

                <!-- Para: -->
                <div class="bg-white dark:bg-[#031d14] p-6 rounded-xl shadow-sm border border-gray-100 dark:border-[#083d2c] space-y-2 text-sm">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400 border-b border-gray-100 dark:border-[#083d2c] pb-2 mb-3">
                        Para: (Cliente / Solicitante)
                    </h3>
                    <div><strong class="text-gray-700 dark:text-emerald-300">Razão Social:</strong> {{ $proposta->para_razao_social }}</div>
                    <div><strong class="text-gray-700 dark:text-emerald-300">Fantasia:</strong> {{ $proposta->para_fantasia ?: '—' }}</div>
                    <div>
                        <strong class="text-gray-700 dark:text-emerald-300">CNPJ / CPF:</strong> {{ $proposta->para_cnpj_cpf ?: '—' }}
                        | <strong class="text-gray-700 dark:text-emerald-300">Inscrição Estadual:</strong> {{ $proposta->para_ie ?: '—' }}
                    </div>
                    <div>
                        <strong class="text-gray-700 dark:text-emerald-300">Endereço:</strong>
                        {{ $proposta->para_endereco ?: '—' }}{{ $proposta->para_bairro ? " - {$proposta->para_bairro}" : '' }}{{ $proposta->para_cidade ? " - {$proposta->para_cidade}" : '' }}{{ $proposta->para_uf ? "/{$proposta->para_uf}" : '' }}{{ $proposta->para_cep ? " (CEP: {$proposta->para_cep})" : '' }}
                    </div>
                    <div class="pt-1 border-t border-gray-100 dark:border-[#083d2c]">
                        <strong class="text-gray-700 dark:text-emerald-300">Solicitante:</strong> {{ $proposta->para_solicitante ?: '—' }}
                        | <strong class="text-gray-700 dark:text-emerald-300">Celular:</strong> {{ $proposta->para_celular ?: '—' }}
                        | <strong class="text-gray-700 dark:text-emerald-300">Email:</strong> {{ $proposta->para_email ?: '—' }}
                    </div>
                </div>
            </div>

            <!-- Períodos -->
            <div class="bg-white dark:bg-[#031d14] p-6 rounded-xl shadow-sm border border-gray-100 dark:border-[#083d2c]">
                <h3 class="text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-emerald-300 mb-4">Período(s)</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="p-3 rounded-lg bg-gray-50 dark:bg-[#021811] border border-gray-200 dark:border-[#083d2c]">
                        <div class="text-[11px] font-bold uppercase text-gray-500 dark:text-emerald-300/70">Saída</div>
                        <div class="text-sm font-bold text-gray-900 dark:text-white mt-0.5">{{ $fmtPeriodo($proposta->data_saida, $proposta->hora_saida) }}</div>
                    </div>
                    <div class="p-3 rounded-lg bg-gray-50 dark:bg-[#021811] border border-gray-200 dark:border-[#083d2c]">
                        <div class="text-[11px] font-bold uppercase text-gray-500 dark:text-emerald-300/70">Início</div>
                        <div class="text-sm font-bold text-gray-900 dark:text-white mt-0.5">{{ $fmtPeriodo($proposta->data_inicio, $proposta->hora_inicio) }}</div>
                    </div>
                    <div class="p-3 rounded-lg bg-gray-50 dark:bg-[#021811] border border-gray-200 dark:border-[#083d2c]">
                        <div class="text-[11px] font-bold uppercase text-gray-500 dark:text-emerald-300/70">Fim</div>
                        <div class="text-sm font-bold text-gray-900 dark:text-white mt-0.5">{{ $fmtPeriodo($proposta->data_fim, $proposta->hora_fim) }}</div>
                    </div>
                    <div class="p-3 rounded-lg bg-gray-50 dark:bg-[#021811] border border-gray-200 dark:border-[#083d2c]">
                        <div class="text-[11px] font-bold uppercase text-gray-500 dark:text-emerald-300/70">Retorno</div>
                        <div class="text-sm font-bold text-gray-900 dark:text-white mt-0.5">{{ $fmtPeriodo($proposta->data_retorno, $proposta->hora_retorno) }}</div>
                    </div>
                </div>
            </div>

            <!-- Produto(s) e Totais -->
            <div class="bg-white dark:bg-[#031d14] rounded-xl shadow-sm border border-gray-100 dark:border-[#083d2c] overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-[#083d2c]">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-emerald-300">Produto(s) & Valores</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-700 dark:text-emerald-100">
                        <thead class="bg-gray-50 dark:bg-[#02140e] text-xs uppercase font-semibold text-gray-600 dark:text-emerald-300 border-b border-gray-100 dark:border-[#083d2c]">
                            <tr>
                                <th class="px-6 py-3">Produto(s)</th>
                                <th class="px-6 py-3 text-center">Qtd</th>
                                <th class="px-6 py-3 text-right">Valor/Período</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-[#083d2c]">
                            @forelse($proposta->itens as $item)
                                <tr>
                                    <td class="px-6 py-3.5 font-medium text-gray-900 dark:text-white">{{ $item->descricao }}</td>
                                    <td class="px-6 py-3.5 text-center font-bold">{{ $item->quantidade }}</td>
                                    <td class="px-6 py-3.5 text-right font-semibold">R$ {{ number_format((float) $item->valor_periodo, 2, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-6 text-center text-gray-400 dark:text-emerald-300/60">
                                        Nenhum item cadastrado nesta proposta.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 p-6 bg-gray-50 dark:bg-[#02140e] border-t border-gray-100 dark:border-[#083d2c] text-sm">
                    <div class="text-center p-3 rounded-lg bg-white dark:bg-[#031d14] border border-gray-200 dark:border-[#083d2c]">
                        <span class="text-xs text-gray-500 dark:text-emerald-300/70 block">Total</span>
                        <strong class="text-base text-gray-900 dark:text-white">R$ {{ number_format((float) $proposta->valor_total, 2, ',', '.') }}</strong>
                    </div>
                    <div class="text-center p-3 rounded-lg bg-white dark:bg-[#031d14] border border-gray-200 dark:border-[#083d2c]">
                        <span class="text-xs text-gray-500 dark:text-emerald-300/70 block">Total Desconto</span>
                        <strong class="text-base text-amber-600 dark:text-amber-400">R$ {{ number_format((float) $proposta->valor_desconto, 2, ',', '.') }}</strong>
                    </div>
                    <div class="text-center p-3 rounded-lg bg-emerald-50 dark:bg-[#04291c] border border-emerald-200 dark:border-emerald-700/60">
                        <span class="text-xs text-emerald-700 dark:text-emerald-300 block font-bold">Total Geral</span>
                        <strong class="text-lg text-emerald-800 dark:text-amber-300">R$ {{ number_format((float) $proposta->valor_total_geral, 2, ',', '.') }}</strong>
                    </div>
                </div>
            </div>

            <!-- Pagamento(s) e Observações -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white dark:bg-[#031d14] p-6 rounded-xl shadow-sm border border-gray-100 dark:border-[#083d2c]">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-emerald-300 mb-2">Pagamento(s)</h3>
                    <div class="text-sm text-gray-700 dark:text-emerald-100/90">
                        {!! !empty(trim((string) $proposta->pagamentos)) ? nl2br(e($proposta->pagamentos)) : '<span class="text-gray-400 dark:text-emerald-400/50">Nenhuma condição informada.</span>' !!}
                    </div>
                </div>

                <div class="bg-white dark:bg-[#031d14] p-6 rounded-xl shadow-sm border border-gray-100 dark:border-[#083d2c]">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-emerald-300 mb-2">Observações</h3>
                    <div class="text-sm text-gray-700 dark:text-emerald-100/90">
                        {!! !empty(trim((string) $proposta->observacoes)) ? nl2br(e($proposta->observacoes)) : '<span class="text-gray-400 dark:text-emerald-400/50">Sem observações.</span>' !!}
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
