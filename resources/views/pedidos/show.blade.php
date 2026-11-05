<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <h2 class="font-bold text-2xl text-gray-800 dark:text-white leading-tight">
                        Pedido #{{ $pedido->id }}
                    </h2>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold {{ $pedido->status_color }}">
                        {{ $pedido->status_label }}
                    </span>
                </div>
                <p class="text-sm text-gray-500 dark:text-emerald-300/70 mt-1">Cliente: <strong class="text-gray-900 dark:text-white">{{ $pedido->cliente->nome }}</strong> | Montador: <strong class="text-gray-900 dark:text-white">{{ $pedido->montador ? $pedido->montador->name : 'Não designado' }}</strong></p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('pedidos.index') }}" class="px-3.5 py-1.5 bg-gray-100 dark:bg-[#063325] hover:bg-gray-200 dark:hover:bg-[#0a4835] text-gray-700 dark:text-emerald-100 text-xs font-semibold rounded-lg transition border border-transparent dark:border-emerald-700/40">
                    &larr; Voltar
                </a>

                <!-- Botão de Contrato do Pedido -->
                @if(!auth()->user()->isMontador())
                    <a href="{{ route('pedidos.contrato.ver', $pedido) }}" target="_blank" class="px-3.5 py-1.5 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-slate-950 font-bold text-xs rounded-lg shadow-xs transition flex items-center gap-1.5" title="Visualizar Contrato deste Pedido em PDF">
                        <svg class="w-3.5 h-3.5 text-slate-950" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Contrato do Pedido (PDF)</span>
                    </a>
                @endif

                @foreach($pedido->ordensServico as $osHeader)
                    <a href="{{ route('os.pdf', $osHeader) }}" target="_blank" class="px-2.5 py-1.5 bg-white dark:bg-[#031d14] border border-gray-200 dark:border-[#083d2c] hover:bg-gray-50 dark:hover:bg-[#063325] text-gray-800 dark:text-emerald-100 text-xs font-semibold rounded-lg shadow-xs transition flex items-center gap-1.5" title="{{ $osHeader->tipo_label }} (Status: {{ $osHeader->status_label }})">
                        <svg class="w-3.5 h-3.5 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span class="font-bold">{{ $osHeader->numero_os }}</span>
                        <span class="text-[10px] px-1.5 py-0.2 rounded font-bold {{ $osHeader->tipo_badge_color }}">{{ $osHeader->tipo_label }}</span>
                    </a>
                @endforeach

                <button type="button" onclick="document.getElementById('modal-gerar-nova-os').classList.remove('hidden')" class="px-3.5 py-1.5 bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs rounded-lg shadow-xs transition flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    + Gerar O.S.
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- FLUXO / BOTÕES DE AÇÃO INTERATIVOS CONFORME O STATUS -->
            <div class="bg-gradient-to-r from-emerald-50 to-teal-50 dark:from-[#02281e] dark:to-[#033628] border border-emerald-200 dark:border-emerald-700/60 p-6 rounded-xl shadow-sm transition-colors">
                <h3 class="text-base font-bold text-gray-900 dark:text-white mb-2 flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    Ações de Operação (Fluxo do Pedido)
                </h3>
                <p class="text-xs text-gray-600 dark:text-emerald-300/70 mb-4">Atualize o andamento da montagem/desmontagem deste pedido:</p>

                <div class="flex flex-wrap gap-3 items-center">
                    {{-- 1. BOTÃO "TENDA INSTALADA": Apenas no fluxo de montagem/instalação --}}
                    @if(in_array($pedido->status, ['pendente', 'os_gerada', 'em_instalacao']))
                        <button type="button" onclick="document.getElementById('modal-finalizar-instalacao').classList.remove('hidden')" class="px-6 py-3 bg-gradient-to-r from-emerald-700 to-emerald-600 hover:from-emerald-800 hover:to-emerald-700 text-amber-300 font-bold rounded-xl shadow-md hover:shadow-lg transition text-sm flex items-center gap-2 border border-amber-400/40 cursor-pointer">
                            <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Tenda instalada</span>
                        </button>
                    @endif

                    {{-- 2. BOTÃO "TENDA RETIRADA": Apenas no fluxo de devolução/retirada --}}
                    @if(in_array($pedido->status, ['instalado', 'aguardando_retirada', 'em_retirada']))
                        <form action="{{ route('pedidos.confirmar-retirada', $pedido) }}" method="POST" data-confirm="Confirma que a tenda foi retirada no local?<br><span class='text-xs text-emerald-500 font-medium'>As tendas retornarão automaticamente ao estoque como disponíveis.</span>" data-confirm-title="Confirmar Retirada da Tenda" data-confirm-btn="Sim, Tenda Retirada" data-confirm-type="success">
                            @csrf
                            <button type="submit" class="px-6 py-3 bg-gradient-to-r from-purple-700 to-purple-600 hover:from-purple-800 hover:to-purple-700 text-white font-bold rounded-xl shadow-md hover:shadow-lg transition text-sm flex items-center gap-2 border border-purple-400/30 cursor-pointer">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Tenda retirada</span>
                            </button>
                        </form>
                    @endif

                    {{-- 3. CICLO CONCLUÍDO --}}
                    @if($pedido->status === 'concluido')
                        <span class="inline-flex items-center px-4 py-2.5 bg-green-100 dark:bg-emerald-950/70 text-green-800 dark:text-emerald-300 text-sm font-bold rounded-xl border border-transparent dark:border-emerald-700/50">
                            🎉 Ciclo Completo! Tendas reintegradas ao estoque.
                        </span>
                    @endif

                    {{-- Botão Adicional exclusivo para Administradores --}}
                    @if(auth()->check() && auth()->user()->isAdmin())
                        <button type="button" onclick="document.getElementById('modal-gerar-nova-os').classList.remove('hidden')" class="px-4 py-2.5 bg-gray-100 dark:bg-[#063325] hover:bg-gray-200 dark:hover:bg-[#0a4835] text-gray-700 dark:text-emerald-200 font-semibold rounded-xl transition text-xs flex items-center gap-2 border border-transparent dark:border-emerald-700/40">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            + Gerar Nova O.S. Manual
                        </button>
                    @endif
                </div>
            </div>

            <!-- Seção de Ordens de Serviço do Pedido -->
            <div class="bg-white dark:bg-[#031d14] p-6 rounded-xl shadow-sm border border-gray-100 dark:border-[#083d2c] transition-colors space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-gray-100 dark:border-[#083d2c] pb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-emerald-600/10 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg text-gray-900 dark:text-white flex items-center gap-2">
                                Ordens de Serviço deste Pedido
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/70 dark:text-emerald-300">
                                    {{ $pedido->ordensServico->count() }} O.S. geradas
                                </span>
                            </h3>
                            <p class="text-xs text-gray-500 dark:text-emerald-300/70">Todas as ordens de serviço deste pedido ficam disponíveis abaixo para consulta em PDF, download e envio ao WhatsApp:</p>
                        </div>
                    </div>

                    <button type="button" onclick="document.getElementById('modal-gerar-nova-os').classList.remove('hidden')" class="px-3.5 py-2 bg-emerald-700 hover:bg-emerald-800 text-white font-bold rounded-lg text-xs transition shadow-xs flex items-center gap-1.5 w-fit">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        + Gerar Nova O.S.
                    </button>
                </div>

                @if($pedido->ordensServico->isEmpty())
                    <div class="py-6 text-center text-gray-400 dark:text-emerald-400/60 text-sm">
                        Nenhuma Ordem de Serviço gerada para este pedido ainda.
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($pedido->ordensServico as $os)
                            <div class="p-4 rounded-xl border border-gray-200 dark:border-[#083d2c] bg-gray-50/70 dark:bg-[#021811] flex flex-col justify-between gap-3 shadow-xs hover:border-emerald-500 dark:hover:border-emerald-600 transition">
                                <div class="space-y-2">
                                    <div class="flex items-center justify-between gap-2">
                                        <a href="{{ route('os.pdf', $os) }}" target="_blank" class="font-mono font-bold text-emerald-700 dark:text-amber-400 hover:underline text-sm flex items-center gap-1.5">
                                            <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            {{ $os->numero_os }}
                                        </a>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold {{ $os->status_badge_color }}">
                                            {{ $os->status_label }}
                                        </span>
                                    </div>

                                    <div>
                                        <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-bold {{ $os->tipo_badge_color }}">
                                            {{ $os->tipo_label }}
                                        </span>
                                    </div>

                                    <div class="text-xs text-gray-500 dark:text-emerald-300/70 space-y-1 pt-1">
                                        <div class="flex justify-between">
                                            <span>Emissão:</span>
                                            <strong class="text-gray-700 dark:text-white">{{ $os->created_at->format('d/m/Y H:i') }}</strong>
                                        </div>
                                        @if($os->concluido_em)
                                            <div class="flex justify-between text-emerald-600 dark:text-emerald-400">
                                                <span>Concluído:</span>
                                                <strong>{{ $os->concluido_em->format('d/m/Y H:i') }}</strong>
                                            </div>
                                        @endif
                                        <div class="flex justify-between">
                                            <span>Montador:</span>
                                            <strong class="text-gray-700 dark:text-white">{{ $pedido->montador ? $pedido->montador->name : 'Não informado' }}</strong>
                                        </div>
                                    </div>
                                </div>

                                <div class="pt-2 border-t border-gray-200 dark:border-[#083d2c] flex items-center justify-between gap-2">
                                    <div class="flex items-center gap-1.5">
                                        <a href="{{ route('os.pdf', $os) }}" target="_blank" class="px-2.5 py-1 bg-white dark:bg-[#031d14] hover:bg-gray-100 dark:hover:bg-[#063325] text-gray-700 dark:text-emerald-200 border border-gray-200 dark:border-[#083d2c] rounded-md text-xs font-semibold transition flex items-center gap-1" title="Visualizar PDF">
                                            <svg class="w-3.5 h-3.5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            Ver PDF
                                        </a>

                                        <a href="{{ route('os.download', $os) }}" class="p-1 bg-white dark:bg-[#031d14] hover:bg-gray-100 dark:hover:bg-[#063325] text-gray-700 dark:text-emerald-200 border border-gray-200 dark:border-[#083d2c] rounded-md text-xs font-semibold transition" title="Baixar PDF">
                                            <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                        </a>
                                    </div>

                                    @if($pedido->montador && $pedido->montador->whatsapp)
                                        <form action="{{ route('os.enviar-whatsapp', $os) }}" method="POST" data-confirm="Deseja reenviar a <strong>{{ $os->numero_os }} ({{ $os->tipo_label }})</strong> para o WhatsApp do montador {{ $pedido->montador->name }}?" data-confirm-title="Reenviar O.S. via WhatsApp" data-confirm-btn="Sim, Enviar" data-confirm-type="success">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1 bg-emerald-600/10 hover:bg-emerald-600 text-emerald-700 hover:text-white dark:bg-emerald-950/60 dark:text-emerald-300 dark:hover:bg-emerald-700 border border-emerald-300/50 dark:border-emerald-700/60 rounded-md text-xs font-semibold transition flex items-center gap-1" title="Enviar para WhatsApp do Montador">
                                                <svg class="w-3.5 h-3.5 text-emerald-500 hover:text-white shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-5.805 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                                                WhatsApp
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Dados do Pedido, Cliente e Contrato -->
            <div class="grid grid-cols-1 {{ auth()->user()->isMontador() ? 'lg:grid-cols-2' : 'lg:grid-cols-3' }} gap-6">
                <!-- Informações do Cliente & Local -->
                <div class="bg-white dark:bg-[#031d14] p-6 rounded-xl shadow-sm border border-gray-100 dark:border-[#083d2c] space-y-4 transition-colors">
                    <h3 class="font-bold text-lg text-gray-900 dark:text-white border-b border-gray-100 dark:border-[#083d2c] pb-2">Informações do Cliente & Local</h3>
                    
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500 dark:text-emerald-300/70">Nome:</span>
                            <span class="font-bold text-gray-900 dark:text-white">{{ $pedido->cliente->nome }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500 dark:text-emerald-300/70">Telefone / WhatsApp:</span>
                            <span class="font-semibold text-emerald-600 dark:text-amber-400">{{ $pedido->cliente->telefone }}</span>
                        </div>
                        @if($pedido->nome_responsavel)
                            <div class="flex justify-between">
                                <span class="text-gray-500 dark:text-emerald-300/70">Responsável / Solicitante:</span>
                                <span class="font-semibold text-gray-900 dark:text-white">{{ $pedido->nome_responsavel }}</span>
                            </div>
                        @endif
                        @if($pedido->telefone_responsavel)
                            <div class="flex justify-between">
                                <span class="text-gray-500 dark:text-emerald-300/70">Telefone Responsável:</span>
                                <span class="font-semibold text-emerald-600 dark:text-amber-400">{{ $pedido->telefone_responsavel }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between">
                            <span class="text-gray-500 dark:text-emerald-300/70">Endereço de Montagem:</span>
                            <span class="font-medium text-gray-900 dark:text-white text-right">{{ $pedido->endereco_instalacao }}</span>
                        </div>
                        @if($pedido->cidade_instalacao)
                            <div class="flex justify-between">
                                <span class="text-gray-500 dark:text-emerald-300/70">Cidade:</span>
                                <span class="font-medium text-gray-900 dark:text-white">{{ $pedido->cidade_instalacao }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between">
                            <span class="text-gray-500 dark:text-emerald-300/70">Observações:</span>
                            <span class="font-medium text-gray-800 dark:text-emerald-100 text-right">{{ $pedido->observacoes ?: 'Nenhuma' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Datas e Montador -->
                <div class="bg-white dark:bg-[#031d14] p-6 rounded-xl shadow-sm border border-gray-100 dark:border-[#083d2c] space-y-4 transition-colors">
                    <h3 class="font-bold text-lg text-gray-900 dark:text-white border-b border-gray-100 dark:border-[#083d2c] pb-2">Programação e Montador</h3>
                    
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500 dark:text-emerald-300/70">Data de Instalação:</span>
                            <span class="font-bold text-emerald-700 dark:text-emerald-400">{{ $pedido->data_instalacao->format('d/m/Y') }} às {{ $pedido->hora_instalacao ? substr($pedido->hora_instalacao, 0, 5) : 'A combinar' }}</span>
                        </div>
                        @if($pedido->data_evento)
                            <div class="flex justify-between">
                                <span class="text-gray-500 dark:text-emerald-300/70">Data do Evento:</span>
                                <span class="font-bold text-amber-600 dark:text-amber-400">{{ $pedido->data_evento->format('d/m/Y') }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between">
                            <span class="text-gray-500 dark:text-emerald-300/70">Data de Retirada:</span>
                            <span class="font-bold text-purple-700 dark:text-purple-400">{{ $pedido->data_retirada->format('d/m/Y') }} às {{ $pedido->hora_retirada ? substr($pedido->hora_retirada, 0, 5) : 'A combinar' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500 dark:text-emerald-300/70">Montador Responsável:</span>
                            <span class="font-semibold text-gray-900 dark:text-white">{{ $pedido->montador ? $pedido->montador->name : 'Nenhum' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500 dark:text-emerald-300/70">WhatsApp Montador:</span>
                            <span class="font-semibold text-emerald-600 dark:text-emerald-400">{{ $pedido->montador->whatsapp ?? 'Não informado' }}</span>
                        </div>
                        @if(!auth()->user()->isMontador() && $pedido->valor_total)
                            <div class="flex justify-between border-t border-gray-100 dark:border-[#083d2c] pt-2">
                                <span class="text-gray-500 dark:text-emerald-300/70 font-semibold">Valor da Locação:</span>
                                <span class="font-black text-gray-900 dark:text-white text-base">R$ {{ number_format($pedido->valor_total, 2, ',', '.') }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                @if(!auth()->user()->isMontador())
                    <!-- Contrato de Locação Digital -->
                    <div class="bg-white dark:bg-[#031d14] p-6 rounded-xl shadow-sm border border-gray-100 dark:border-[#083d2c] space-y-4 transition-colors flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between border-b border-gray-100 dark:border-[#083d2c] pb-2">
                                <h3 class="font-bold text-lg text-gray-900 dark:text-white flex items-center gap-2">
                                    <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    Contrato de Locação
                                </h3>
                                <span class="text-xs font-mono font-bold text-amber-600 dark:text-amber-400">
                                    Nº {{ sprintf('%04d', $pedido->id) }}
                                </span>
                            </div>

                            <div class="space-y-2 text-sm pt-3">
                                <div class="flex justify-between">
                                    <span class="text-gray-500 dark:text-emerald-300/70">Cliente:</span>
                                    <span class="font-bold text-gray-900 dark:text-white truncate max-w-[170px]" title="{{ $pedido->cliente->nome }}">{{ $pedido->cliente->nome }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500 dark:text-emerald-300/70">Total da Locação:</span>
                                    <span class="font-bold text-emerald-600 dark:text-emerald-400">
                                        {{ $pedido->valor_total ? 'R$ ' . number_format($pedido->valor_total, 2, ',', '.') : 'A combinar / Sem valor' }}
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500 dark:text-emerald-300/70">Período:</span>
                                    <span class="font-medium text-gray-800 dark:text-emerald-100 text-xs text-right">
                                        {{ $pedido->data_instalacao->format('d/m/Y') }} a {{ $pedido->data_retirada->format('d/m/Y') }}
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500 dark:text-emerald-300/70">Status:</span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-amber-500/15 text-amber-600 dark:text-amber-300 border border-amber-500/30">
                                        Dinâmico do Pedido
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="pt-3 flex flex-col gap-2 border-t border-gray-100 dark:border-[#083d2c]">
                            <a href="{{ route('pedidos.contrato.ver', $pedido) }}" target="_blank" class="w-full text-center px-3 py-2 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-slate-950 font-bold rounded-lg text-xs shadow-xs transition flex items-center justify-center gap-1.5">
                                <svg class="w-4 h-4 text-slate-950" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                Visualizar Contrato (PDF)
                            </a>
                            <a href="{{ route('pedidos.contrato.download', $pedido) }}" class="w-full text-center px-3 py-2 bg-gray-100 dark:bg-[#063325] hover:bg-gray-200 dark:hover:bg-[#0a4835] text-gray-800 dark:text-emerald-100 font-semibold rounded-lg text-xs transition flex items-center justify-center gap-1.5 border border-transparent dark:border-emerald-700/40">
                                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                Baixar Contrato em PDF
                            </a>
                        </div>
                    </div>
                @endif
            </div>
            </div>

            <!-- Tendas Alugadas -->
            <div class="bg-white dark:bg-[#031d14] p-6 rounded-xl shadow-sm border border-gray-100 dark:border-[#083d2c] transition-colors">
                <h3 class="font-bold text-lg text-gray-900 dark:text-white mb-4">Tendas Selecionadas neste Pedido</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                    @foreach($pedido->tendas as $tenda)
                        <div class="p-4 bg-gray-50 dark:bg-[#02140e] rounded-lg border border-gray-200 dark:border-[#083d2c]">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-gray-900 dark:text-white">{{ $tenda->nome }}</span>
                                <span class="px-2 py-0.5 bg-emerald-100 dark:bg-emerald-950/70 text-emerald-800 dark:text-emerald-300 border border-transparent dark:border-emerald-700/50 text-xs font-bold rounded-full">
                                    Qtd: {{ $tenda->pivot->quantidade }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-emerald-300/70 mt-1">Dimensões: {{ $tenda->tamanho }}</p>
                            @if($tenda->descricao)
                                <p class="text-xs text-gray-400 dark:text-emerald-400/60 mt-0.5">{{ $tenda->descricao }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Fotos Anexadas pelo Montador (se houver) -->
            @php
                $osInstalacao = $pedido->ordensServico->first(fn($item) => !empty($item->fotos) && count($item->fotos) > 0) ?: $pedido->osInstalacao;
                $osComFotos = $osInstalacao;
                $fotosList = is_array($osInstalacao?->fotos) ? $osInstalacao->fotos : [];
                $fotosUrls = array_map(function($path) {
                    return route('storage.file', ['path' => $path]);
                }, $fotosList);
            @endphp
            @if($osInstalacao && count($fotosList) > 0)
                <div x-data="{
                    openLightbox: false,
                    currentIndex: 0,
                    photos: @js($fotosUrls),
                    openPhoto(index) {
                        this.currentIndex = index;
                        this.openLightbox = true;
                        document.body.classList.add('overflow-hidden');
                    },
                    closeLightbox() {
                        this.openLightbox = false;
                        document.body.classList.remove('overflow-hidden');
                    },
                    nextPhoto() {
                        if (this.photos.length > 0) {
                            this.currentIndex = (this.currentIndex + 1) % this.photos.length;
                        }
                    },
                    prevPhoto() {
                        if (this.photos.length > 0) {
                            this.currentIndex = (this.currentIndex - 1 + this.photos.length) % this.photos.length;
                        }
                    }
                }"
                @keydown.escape.window="closeLightbox()"
                @keydown.arrow-right.window="if (openLightbox) nextPhoto()"
                @keydown.arrow-left.window="if (openLightbox) prevPhoto()"
                class="bg-white dark:bg-[#031d14] p-6 rounded-xl shadow-sm border border-gray-100 dark:border-[#083d2c] transition-colors">
                    
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-4">
                        <h3 class="font-bold text-lg text-gray-900 dark:text-white flex items-center gap-2">
                            <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            Fotos da Instalação / Conclusão ({{ count($fotosList) }})
                        </h3>
                        <span class="text-xs text-gray-400 dark:text-emerald-400/60 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            Clique em qualquer foto para ampliar na tela
                        </span>
                    </div>

                    @if($osComFotos && $osComFotos->observacoes_montador)
                        <div class="mb-4 p-3 bg-gray-50 dark:bg-[#02140e] rounded-lg text-sm text-gray-700 dark:text-emerald-100 border-l-4 border-emerald-500 shadow-sm">
                            <strong class="text-emerald-700 dark:text-amber-400">Obs do montador:</strong> {{ $osComFotos->observacoes_montador }}
                        </div>
                    @endif

                    <!-- Grade de Fotos com Efeito Hover e Clique para Ampliar -->
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                        @foreach($fotosList as $idx => $foto)
                            @php
                                $fotoUrl = route('storage.file', ['path' => $foto]);
                                $fallbackUrl = Storage::url($foto);
                            @endphp
                            <div class="group relative aspect-video bg-gray-100 dark:bg-[#02140e] rounded-xl overflow-hidden border border-gray-200 dark:border-[#083d2c] shadow-sm hover:shadow-lg transition-all duration-200 cursor-pointer"
                                 @click="openPhoto({{ $idx }})">
                                <img src="{{ $fotoUrl }}" 
                                     alt="Foto instalação #{{ $idx + 1 }}" 
                                     loading="lazy"
                                     class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                                     onerror="if (this.src !== '{{ $fallbackUrl }}') { this.src = '{{ $fallbackUrl }}'; } else { this.onerror=null; this.parentElement.classList.add('flex', 'items-center', 'justify-center', 'text-gray-400'); this.parentElement.innerHTML='<div class=\'p-3 text-center text-xs\'><svg class=\'w-6 h-6 mx-auto mb-1 text-gray-400\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z\'></svg>Foto indisponível</div>'; }">
                                
                                <!-- Overlay Hover com Lupa e Texto -->
                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex flex-col items-center justify-center p-2 text-center pointer-events-none">
                                    <span class="p-2 bg-emerald-600/90 text-white rounded-full shadow-lg transform -translate-y-1 group-hover:translate-y-0 transition-transform">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7"/></svg>
                                    </span>
                                    <span class="text-[11px] font-semibold text-white mt-1.5 drop-shadow">Clique para ampliar</span>
                                </div>

                                <!-- Badge com Índice -->
                                <span class="absolute top-2 left-2 bg-black/60 backdrop-blur-sm text-white text-[10px] font-bold px-2 py-0.5 rounded-full pointer-events-none">
                                    #{{ $idx + 1 }}
                                </span>
                            </div>
                        @endforeach
                    </div>

                    <!-- Modal Lightbox em Tela Cheia para Fotos Ampliadas -->
                    <template x-teleport="body">
                        <div x-show="openLightbox" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0"
                             x-transition:enter-end="opacity-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100"
                             x-transition:leave-end="opacity-0"
                             class="fixed inset-0 z-[100] flex flex-col justify-between bg-black/95 backdrop-blur-md select-none"
                             style="display: none;"
                             @click.self="closeLightbox()">
                            
                            <!-- Barra Superior do Lightbox -->
                            <div class="w-full p-4 flex items-center justify-between text-white border-b border-white/10 bg-black/50 backdrop-blur-sm z-20">
                                <div class="flex items-center gap-3">
                                    <span class="px-3 py-1 rounded-full bg-emerald-600 text-white text-xs font-bold tracking-wide shadow">
                                        Foto <span x-text="currentIndex + 1"></span> de <span x-text="photos.length"></span>
                                    </span>
                                    <span class="text-sm font-semibold text-gray-200 hidden sm:inline">
                                        Instalação - Pedido #{{ $pedido->id }}
                                    </span>
                                </div>

                                <div class="flex items-center gap-2">
                                    <!-- Botão Abrir Original em Nova Aba -->
                                    <a :href="photos[currentIndex]" target="_blank" title="Abrir imagem original em tela cheia" class="px-3 py-1.5 bg-white/10 hover:bg-white/20 text-white text-xs font-semibold rounded-lg transition flex items-center gap-1.5 border border-white/10">
                                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        <span class="hidden sm:inline">Abrir Original</span>
                                    </a>

                                    <!-- Botão Fechar -->
                                    <button type="button" @click="closeLightbox()" title="Fechar (Esc)" class="w-9 h-9 rounded-lg bg-white/10 hover:bg-red-600 text-white flex items-center justify-center transition font-bold text-xl border border-white/10">
                                        &times;
                                    </button>
                                </div>
                            </div>

                            <!-- Área Central com a Imagem Ampliada e Setas -->
                            <div class="relative flex-1 flex items-center justify-center p-3 sm:p-6 overflow-hidden">
                                <!-- Seta Anterior -->
                                <button x-show="photos.length > 1" 
                                        type="button" 
                                        @click.stop="prevPhoto()" 
                                        title="Foto anterior (Seta esquerda)"
                                        class="absolute left-3 sm:left-6 z-30 w-11 h-11 sm:w-14 sm:h-14 rounded-full bg-black/60 hover:bg-emerald-600 text-white flex items-center justify-center transition border border-white/20 shadow-2xl backdrop-blur-sm">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                                </button>

                                <!-- Imagem Ampliada com Container Responsivo -->
                                <div class="relative max-w-full max-h-[75vh] sm:max-h-[80vh] flex items-center justify-center" @click.stop>
                                    <img :src="photos[currentIndex]" 
                                         alt="Foto ampliada da montagem"
                                         class="max-w-full max-h-[75vh] sm:max-h-[80vh] object-contain rounded-xl shadow-2xl border border-white/10 transition-all duration-200">
                                </div>

                                <!-- Seta Próxima -->
                                <button x-show="photos.length > 1" 
                                        type="button" 
                                        @click.stop="nextPhoto()" 
                                        title="Próxima foto (Seta direita)"
                                        class="absolute right-3 sm:right-6 z-30 w-11 h-11 sm:w-14 sm:h-14 rounded-full bg-black/60 hover:bg-emerald-600 text-white flex items-center justify-center transition border border-white/20 shadow-2xl backdrop-blur-sm">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                                </button>
                            </div>

                            <!-- Barra Inferior do Lightbox (Observação e Miniaturas) -->
                            <div class="w-full p-3 bg-black/60 backdrop-blur-sm border-t border-white/10 flex flex-col sm:flex-row items-center justify-between gap-3 z-20">
                                @if($osInstalacao->observacoes_montador)
                                    <div class="text-xs text-emerald-200 max-w-xl truncate px-2">
                                        <strong class="text-amber-400">Obs do montador:</strong> {{ $osInstalacao->observacoes_montador }}
                                    </div>
                                @else
                                    <div class="text-xs text-gray-400 px-2">
                                        Dica: Navegue com as setas ← → do teclado ou deslize para os lados.
                                    </div>
                                @endif

                                <!-- Miniaturas do Carrossel inferior -->
                                <div x-show="photos.length > 1" class="flex items-center gap-2 overflow-x-auto py-1 max-w-full px-2">
                                    <template x-for="(fotoUrl, idx) in photos" :key="idx">
                                        <button type="button" 
                                                @click="currentIndex = idx" 
                                                class="w-12 h-10 rounded-md overflow-hidden border-2 transition shrink-0"
                                                :class="currentIndex === idx ? 'border-amber-400 ring-2 ring-amber-400/50 scale-105' : 'border-white/30 opacity-60 hover:opacity-100'">
                                            <img :src="fotoUrl" alt="Miniatura" class="w-full h-full object-cover">
                                        </button>
                                    </template>
                                </div>
                            </div>

                        </div>
                    </template>
                </div>
            @endif

        </div>
    </div>

    <!-- Modal Anexar Fotos Durante Instalação -->
    <div id="modal-anexar-fotos" class="fixed inset-0 bg-black/60 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white dark:bg-[#031d14] rounded-xl max-w-lg w-full p-6 shadow-2xl relative border border-gray-200 dark:border-[#083d2c]">
            <button onclick="document.getElementById('modal-anexar-fotos').classList.add('hidden')" class="absolute top-3 right-3 text-gray-400 dark:text-emerald-400 hover:text-gray-600 dark:hover:text-white text-xl font-bold">&times;</button>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">📸 Anexar Fotos da Montagem</h3>
            <p class="text-xs text-gray-500 dark:text-emerald-300/70 mb-4">Tire fotos pelo celular para registrar o local ou o andamento da montagem.</p>

            <form action="{{ route('pedidos.anexar-fotos', $pedido) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-emerald-200 mb-1">Tirar ou Selecionar Fotos *</label>
                    <input type="file" name="fotos[]" multiple accept="image/*" capture="environment" required class="w-full border-gray-300 dark:border-emerald-700/60 dark:bg-[#02140e] dark:text-white rounded-lg text-sm file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 dark:file:bg-emerald-950/70 file:text-emerald-700 dark:file:text-emerald-300 hover:file:bg-emerald-100">
                    <p class="text-[11px] text-gray-400 dark:text-emerald-400/60 mt-1">Ao tocar no celular, a câmera abre para fotografar na hora.</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-emerald-200 mb-1">Observações da Montagem (Opcional)</label>
                    <textarea name="observacoes_montador" rows="2" placeholder="Ex: Tenda ancorada com estacas e pesos adicionais..." class="w-full border-gray-300 dark:border-emerald-700/60 dark:bg-[#02140e] dark:text-white placeholder-gray-400 dark:placeholder-emerald-400/40 focus:border-amber-400 focus:ring-amber-400/20 rounded-lg text-sm"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="document.getElementById('modal-anexar-fotos').classList.add('hidden')" class="px-4 py-2 bg-gray-100 dark:bg-[#063325] text-gray-700 dark:text-emerald-200 font-semibold rounded-lg text-sm">Cancelar</button>
                    <button type="submit" class="px-5 py-2 bg-emerald-700 hover:bg-emerald-800 text-white font-bold rounded-lg text-sm shadow">
                        Salvar Fotos
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Informar Tenda Instalada (com fotos e observações) -->
    <div id="modal-finalizar-instalacao" class="fixed inset-0 bg-black/60 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white dark:bg-[#031d14] rounded-xl max-w-lg w-full p-6 shadow-2xl relative border border-gray-200 dark:border-[#083d2c]">
            <button onclick="document.getElementById('modal-finalizar-instalacao').classList.add('hidden')" class="absolute top-3 right-3 text-gray-400 dark:text-emerald-400 hover:text-gray-600 dark:hover:text-white text-xl font-bold">&times;</button>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2 flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Informar Tenda Instalada
            </h3>
            <p class="text-xs text-gray-500 dark:text-emerald-300/70 mb-4">Ao confirmar, a instalação será concluída, a 2ª O.S. (Retirada) será emitida e o WhatsApp principal da empresa será notificado.</p>

            <form action="{{ route('pedidos.finalizar-instalacao', $pedido) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-emerald-200 mb-1">Fotos da Tenda Montada (Opcional)</label>
                    <input type="file" name="fotos[]" multiple accept="image/*" capture="environment" class="w-full border-gray-300 dark:border-emerald-700/60 dark:bg-[#02140e] dark:text-white rounded-lg text-sm file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 dark:file:bg-emerald-950/70 file:text-emerald-700 dark:file:text-emerald-300 hover:file:bg-emerald-100">
                    <p class="text-xs text-gray-400 dark:text-emerald-400/60 mt-1">Você pode selecionar várias fotos ou tirar direto com a câmera do celular.</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-emerald-200 mb-1">Observações da Instalação (Opcional)</label>
                    <textarea name="observacoes_montador" rows="3" placeholder="Ex: Montagem finalizada no gramado com estacas e pesos adicionais..." class="w-full border-gray-300 dark:border-emerald-700/60 dark:bg-[#02140e] dark:text-white placeholder-gray-400 dark:placeholder-emerald-400/40 focus:border-amber-400 focus:ring-amber-400/20 rounded-lg text-sm"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="document.getElementById('modal-finalizar-instalacao').classList.add('hidden')" class="px-4 py-2 bg-gray-100 dark:bg-[#063325] text-gray-700 dark:text-emerald-200 font-semibold rounded-lg text-sm">Cancelar</button>
                    <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-emerald-700 to-emerald-600 hover:from-emerald-800 hover:to-emerald-700 text-amber-300 font-bold rounded-lg text-sm shadow border border-amber-400/40">
                        Confirmar Tenda Instalada
                    </button>
                </div>
            </form>
        </div>
    </div>
    <!-- Modal Gerar Nova Ordem de Serviço -->
    <div id="modal-gerar-nova-os" class="fixed inset-0 bg-black/60 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white dark:bg-[#031d14] rounded-xl max-w-md w-full p-6 shadow-2xl relative border border-gray-200 dark:border-[#083d2c]">
            <button onclick="document.getElementById('modal-gerar-nova-os').classList.add('hidden')" class="absolute top-3 right-3 text-gray-400 dark:text-emerald-400 hover:text-gray-600 dark:hover:text-white text-xl font-bold">&times;</button>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2 flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Gerar Nova Ordem de Serviço
            </h3>
            <p class="text-xs text-gray-500 dark:text-emerald-300/70 mb-4">Gere uma nova O.S. a mais para este pedido. Ela receberá uma numeração exclusiva e ficará disponível no histórico deste pedido.</p>

            <form action="{{ route('pedidos.gerar-os', $pedido) }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-emerald-200 mb-1">Tipo da O.S. (Finalidade) *</label>
                    <select name="tipo" class="w-full border-gray-300 dark:border-emerald-700/60 dark:bg-[#02140e] dark:text-white focus:border-amber-400 focus:ring-amber-400/20 rounded-lg text-sm">
                        <option value="instalacao" {{ in_array($pedido->status, ['pendente', 'os_gerada', 'em_instalacao']) ? 'selected' : '' }}>Montagem / Instalação (OSI)</option>
                        <option value="retirada" {{ in_array($pedido->status, ['aguardando_retirada', 'em_retirada']) ? 'selected' : '' }}>Desmontagem / Retirada (OSR)</option>
                        <option value="conclusao" {{ $pedido->status === 'concluido' ? 'selected' : '' }}>Conclusão / Devolução ao Estoque (OSC)</option>
                        <option value="manutencao">Manutenção / Vistoria Técnica (OSM)</option>
                    </select>
                </div>

                <div class="p-3 bg-gray-50 dark:bg-[#02140e] rounded-lg border border-gray-100 dark:border-[#083d2c] text-xs text-gray-600 dark:text-emerald-300/70 space-y-1">
                    <div>Pedido: <strong>#{{ $pedido->id }} - {{ $pedido->cliente->nome }}</strong></div>
                    <div>Montador: <strong>{{ $pedido->montador ? $pedido->montador->name : 'Nenhum' }}</strong></div>
                    @if($pedido->montador && $pedido->montador->whatsapp)
                        <div class="text-emerald-600 dark:text-emerald-400 font-medium">✓ O montador receberá um aviso automático no WhatsApp.</div>
                    @endif
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="document.getElementById('modal-gerar-nova-os').classList.add('hidden')" class="px-4 py-2 bg-gray-100 dark:bg-[#063325] text-gray-700 dark:text-emerald-200 font-semibold rounded-lg text-sm">Cancelar</button>
                    <button type="submit" class="px-5 py-2 bg-emerald-700 hover:bg-emerald-800 text-white font-bold rounded-lg text-sm shadow">
                        Gerar Ordem de Serviço
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
