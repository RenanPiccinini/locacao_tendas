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
                <a href="{{ route('pedidos.index') }}" class="px-4 py-2 bg-gray-100 dark:bg-[#063325] hover:bg-gray-200 dark:hover:bg-[#0a4835] text-gray-700 dark:text-emerald-100 text-xs font-semibold rounded-lg transition border border-transparent dark:border-emerald-700/40">
                    &larr; Voltar
                </a>

                @if($pedido->osInstalacao)
                    <a href="{{ route('os.pdf', $pedido->osInstalacao) }}" target="_blank" class="px-3 py-2 bg-white dark:bg-[#031d14] border border-gray-300 dark:border-[#083d2c] hover:bg-gray-50 dark:hover:bg-[#063325] text-gray-700 dark:text-emerald-100 text-xs font-semibold rounded-lg shadow-sm transition flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        PDF O.S. Instalação
                    </a>
                @endif

                @if($pedido->osRetirada)
                    <a href="{{ route('os.pdf', $pedido->osRetirada) }}" target="_blank" class="px-3 py-2 bg-white dark:bg-[#031d14] border border-gray-300 dark:border-[#083d2c] hover:bg-gray-50 dark:hover:bg-[#063325] text-gray-700 dark:text-emerald-100 text-xs font-semibold rounded-lg shadow-sm transition flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        PDF O.S. Retirada
                    </a>
                @endif
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

                <div class="flex flex-wrap gap-3">
                    {{-- Status: PENDENTE --}}
                    @if($pedido->status === 'pendente')
                        <form action="{{ route('pedidos.gerar-os', $pedido) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-5 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white font-bold rounded-lg shadow transition text-sm flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                Gerar O.S. e Avisar Montador no WhatsApp
                            </button>
                        </form>
                    @endif

                    {{-- Status: OS GERADA (Montador a caminho ou pronto pra iniciar) --}}
                    @if(in_array($pedido->status, ['os_gerada', 'pendente']))
                        <form action="{{ route('pedidos.iniciar-instalacao', $pedido) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold rounded-lg shadow transition text-sm flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Estou em Processo de Instalação
                            </button>
                        </form>
                    @endif

                    {{-- Status: EM INSTALAÇÃO (Montador pode tirar/anexar fotos e concluir a montagem) --}}
                    @if($pedido->status === 'em_instalacao')
                        <button type="button" onclick="document.getElementById('modal-anexar-fotos').classList.remove('hidden')" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg shadow transition text-sm flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            📸 Tirar / Anexar Fotos
                        </button>

                        <button type="button" onclick="document.getElementById('modal-finalizar-instalacao').classList.remove('hidden')" class="px-5 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white font-bold rounded-lg shadow transition text-sm flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            ✅ Concluir Montagem
                        </button>
                    @endif

                    {{-- Status: AGUARDANDO RETIRADA --}}
                    @if($pedido->status === 'aguardando_retirada')
                        <form action="{{ route('pedidos.iniciar-retirada', $pedido) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-5 py-2.5 bg-purple-600 hover:bg-purple-700 text-white font-bold rounded-lg shadow transition text-sm flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                A Caminho da Retirada (Iniciar Retirada)
                            </button>
                        </form>

                        <form action="{{ route('pedidos.confirmar-retirada', $pedido) }}" method="POST" data-confirm="Confirma que as tendas foram retiradas no local?<br><span class='text-xs text-emerald-500 font-medium'>Elas retornarão automaticamente ao estoque como disponíveis.</span>" data-confirm-title="Confirmar Retirada" data-confirm-btn="Sim, Confirmar Retirada" data-confirm-type="success">
                            @csrf
                            <button type="submit" class="px-5 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white font-bold rounded-lg shadow transition text-sm flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Confirmar Retirada & Devolver ao Estoque
                            </button>
                        </form>
                    @endif

                    {{-- Status: EM RETIRADA --}}
                    @if($pedido->status === 'em_retirada')
                        <form action="{{ route('pedidos.confirmar-retirada', $pedido) }}" method="POST" data-confirm="Confirma que as tendas foram retiradas no local?<br><span class='text-xs text-emerald-500 font-medium'>Elas retornarão automaticamente ao estoque como disponíveis.</span>" data-confirm-title="Confirmar Retirada" data-confirm-btn="Sim, Confirmar Retirada" data-confirm-type="success">
                            @csrf
                            <button type="submit" class="px-5 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white font-bold rounded-lg shadow transition text-sm flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Retirado! Concluir e Retornar ao Estoque
                            </button>
                        </form>
                    @endif

                    @if($pedido->status === 'concluido')
                        <span class="inline-flex items-center px-4 py-2 bg-green-100 dark:bg-emerald-950/70 text-green-800 dark:text-emerald-300 text-sm font-bold rounded-lg border border-transparent dark:border-emerald-700/50">
                            🎉 Ciclo Completo! Tendas reintegradas ao estoque.
                        </span>
                    @endif
                </div>
            </div>

            <!-- Dados do Pedido e Cliente -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
                        @if($pedido->valor_total)
                            <div class="flex justify-between border-t border-gray-100 dark:border-[#083d2c] pt-2">
                                <span class="text-gray-500 dark:text-emerald-300/70 font-semibold">Valor da Locação:</span>
                                <span class="font-black text-gray-900 dark:text-white text-base">R$ {{ number_format($pedido->valor_total, 2, ',', '.') }}</span>
                            </div>
                        @endif
                    </div>
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
                $osInstalacao = $pedido->osInstalacao;
            @endphp
            @if($osInstalacao && !empty($osInstalacao->fotos))
                <div class="bg-white dark:bg-[#031d14] p-6 rounded-xl shadow-sm border border-gray-100 dark:border-[#083d2c] transition-colors">
                    <h3 class="font-bold text-lg text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Fotos da Instalação / Conclusão
                    </h3>

                    @if($osInstalacao->observacoes_montador)
                        <div class="mb-4 p-3 bg-gray-50 dark:bg-[#02140e] rounded text-sm text-gray-700 dark:text-emerald-100 border-l-4 border-emerald-500">
                            <strong>Obs do montador:</strong> {{ $osInstalacao->observacoes_montador }}
                        </div>
                    @endif

                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                        @foreach($osInstalacao->fotos as $foto)
                            <a href="{{ Storage::url($foto) }}" target="_blank" class="block aspect-video bg-gray-100 dark:bg-[#02140e] rounded-lg overflow-hidden border border-gray-200 dark:border-[#083d2c] hover:opacity-90 shadow-sm transition">
                                <img src="{{ Storage::url($foto) }}" alt="Foto instalação" class="w-full h-full object-cover">
                            </a>
                        @endforeach
                    </div>
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

    <!-- Modal Finalizar Instalação (com upload de fotos) -->
    <div id="modal-finalizar-instalacao" class="fixed inset-0 bg-black/60 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white dark:bg-[#031d14] rounded-xl max-w-lg w-full p-6 shadow-2xl relative border border-gray-200 dark:border-[#083d2c]">
            <button onclick="document.getElementById('modal-finalizar-instalacao').classList.add('hidden')" class="absolute top-3 right-3 text-gray-400 dark:text-emerald-400 hover:text-gray-600 dark:hover:text-white text-xl font-bold">&times;</button>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Finalizar Instalação da Tenda</h3>
            <p class="text-xs text-gray-500 dark:text-emerald-300/70 mb-4">Ao concluir, o status mudará para "Aguardando Retirada" e a <strong>O.S. de Retorno</strong> será gerada automaticamente.</p>

            <form action="{{ route('pedidos.finalizar-instalacao', $pedido) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-emerald-200 mb-1">Anexar Fotos Finais da Tenda Montada</label>
                    <input type="file" name="fotos[]" multiple accept="image/*" capture="environment" class="w-full border-gray-300 dark:border-emerald-700/60 dark:bg-[#02140e] dark:text-white rounded-lg text-sm file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 dark:file:bg-emerald-950/70 file:text-emerald-700 dark:file:text-emerald-300 hover:file:bg-emerald-100">
                    <p class="text-xs text-gray-400 dark:text-emerald-400/60 mt-1">Você pode selecionar várias fotos ou tirar com a câmera do celular.</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-emerald-200 mb-1">Observações do Montador</label>
                    <textarea name="observacoes_montador" rows="3" placeholder="Ex: Tenda montada no gramado com estacas e pesos adicionais..." class="w-full border-gray-300 dark:border-emerald-700/60 dark:bg-[#02140e] dark:text-white placeholder-gray-400 dark:placeholder-emerald-400/40 focus:border-amber-400 focus:ring-amber-400/20 rounded-lg text-sm"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="document.getElementById('modal-finalizar-instalacao').classList.add('hidden')" class="px-4 py-2 bg-gray-100 dark:bg-[#063325] text-gray-700 dark:text-emerald-200 font-semibold rounded-lg text-sm">Cancelar</button>
                    <button type="submit" class="px-5 py-2 bg-emerald-700 hover:bg-emerald-800 text-white font-bold rounded-lg text-sm shadow">
                        Confirmar Conclusão & Gerar O.S. Retorno
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
