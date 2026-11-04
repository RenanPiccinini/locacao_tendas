<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 dark:text-white leading-tight">
                    Novo Pedido de Locação
                </h2>
                <p class="text-sm text-gray-500 dark:text-emerald-400/80">Cadastre os dados da locação, selecione tendas e designe o montador</p>
            </div>
            <a href="{{ route('pedidos.index') }}" class="text-sm font-semibold text-emerald-700 dark:text-amber-400 hover:text-emerald-900 dark:hover:text-amber-300">&larr; Voltar aos Pedidos</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            @if($errors->any())
                <div class="mb-6 p-4 bg-red-50 dark:bg-red-950/40 border-l-4 border-red-500 text-red-800 dark:text-red-200 rounded-lg shadow-xs">
                    <div class="font-bold mb-1">Atenção! Verifique os seguintes campos:</div>
                    <ul class="list-disc list-inside text-sm space-y-1">
                        @foreach($errors->all() as $erro)
                            <li>{{ $erro }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('pedidos.store') }}" method="POST" class="bg-white dark:bg-[#031d14] p-6 sm:p-8 rounded-xl shadow-xs border border-gray-200 dark:border-[#083d2c] space-y-6">
                @csrf

                <!-- Cliente e Montador -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-emerald-200 mb-1">Cliente *</label>
                        <select name="cliente_id" required class="w-full bg-white dark:bg-[#02140e] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm">
                            <option value="">Selecione o cliente...</option>
                            @foreach($clientes as $c)
                                <option value="{{ $c->id }}" {{ old('cliente_id') == $c->id ? 'selected' : '' }}>
                                    {{ $c->nome }} ({{ $c->telefone }})
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-400 dark:text-emerald-400/60 mt-1">Precisa de um novo cliente? <a href="{{ route('clientes.create') }}" class="text-amber-600 dark:text-amber-400 underline font-semibold" target="_blank">Cadastre aqui</a>.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-emerald-200 mb-1">Montador Responsável</label>
                        <select name="montador_id" class="w-full bg-white dark:bg-[#02140e] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm">
                            <option value="">Selecione o montador (opcional)...</option>
                            @foreach($montadores as $m)
                                <option value="{{ $m->id }}" {{ old('montador_id') == $m->id ? 'selected' : '' }}>
                                    {{ $m->name }} (WPP: {{ $m->whatsapp ?? 'Sem número' }})
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-400 dark:text-emerald-400/60 mt-1">Ao gerar a O.S., o montador recebe notificação no WhatsApp.</p>
                    </div>
                </div>

                <!-- Datas e Horários -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-emerald-200 mb-1">Data Instalação *</label>
                        <input type="date" name="data_instalacao" value="{{ old('data_instalacao', date('Y-m-d')) }}" required class="w-full bg-white dark:bg-[#02140e] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-emerald-200 mb-1">Hora Instalação</label>
                        <input type="time" name="hora_instalacao" value="{{ old('hora_instalacao', '09:00') }}" class="w-full bg-white dark:bg-[#02140e] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-emerald-200 mb-1">Data do Evento</label>
                        <input type="date" name="data_evento" value="{{ old('data_evento') }}" class="w-full bg-white dark:bg-[#02140e] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-emerald-200 mb-1">Data Retirada *</label>
                        <input type="date" name="data_retirada" value="{{ old('data_retirada', date('Y-m-d', strtotime('+2 days'))) }}" required class="w-full bg-white dark:bg-[#02140e] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-emerald-200 mb-1">Hora Retirada</label>
                        <input type="time" name="hora_retirada" value="{{ old('hora_retirada', '17:00') }}" class="w-full bg-white dark:bg-[#02140e] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm">
                    </div>
                </div>

                <!-- Endereço de Instalação -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-emerald-200 mb-1">Endereço da Montagem *</label>
                        <input type="text" name="endereco_instalacao" value="{{ old('endereco_instalacao') }}" required placeholder="Rua, número, bairro, ponto de referência" class="w-full bg-white dark:bg-[#02140e] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-emerald-200 mb-1">Cidade</label>
                        <input type="text" name="cidade_instalacao" value="{{ old('cidade_instalacao') }}" placeholder="Ex: São José do Rio Preto" class="w-full bg-white dark:bg-[#02140e] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm">
                    </div>
                </div>

                <!-- Seleção de Tendas do Estoque -->
                <div class="border-t border-gray-200 dark:border-[#083d2c] pt-6">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="font-bold text-gray-800 dark:text-white text-base">Tendas para a Locação *</h3>
                        <span class="text-xs text-amber-600 dark:text-amber-400 font-semibold">Marque pelo menos uma tenda</span>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-emerald-400/80 mb-4">Marque a caixa de seleção da tenda desejada e informe a quantidade:</p>

                    <div class="space-y-3">
                        @foreach($tendas as $tenda)
                            <div class="flex items-center justify-between p-3.5 border border-gray-200 dark:border-[#083d2c] rounded-lg hover:border-amber-400 dark:hover:border-amber-400 bg-gray-50/50 dark:bg-[#021811] transition">
                                <div class="flex items-center gap-3">
                                    <input type="checkbox" id="tenda_{{ $tenda->id }}" name="tendas_selecionadas[]" value="{{ $tenda->id }}" class="rounded text-amber-500 focus:ring-amber-400 w-4 h-4 cursor-pointer">
                                    <label for="tenda_{{ $tenda->id }}" class="text-sm cursor-pointer select-none">
                                        <span class="font-bold text-gray-900 dark:text-slate-100">{{ $tenda->nome }}</span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400 ml-2">Dimensões: {{ $tenda->tamanho }}</span>
                                        <span class="text-xs text-emerald-600 dark:text-emerald-400 ml-2 font-semibold">({{ $tenda->quantidade_disponivel }} disp. no estoque)</span>
                                    </label>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-medium text-gray-600 dark:text-emerald-200">Quantidade:</span>
                                    <input type="number" name="quantidades[{{ $tenda->id }}]" value="1" min="1" max="{{ max(1, $tenda->quantidade_disponivel) }}" class="w-20 bg-white dark:bg-[#02140e] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm text-center font-bold">
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Valor e Observações -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 border-t border-gray-200 dark:border-[#083d2c] pt-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-emerald-200 mb-1">Valor Total da Locação (R$)</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-500 dark:text-amber-400 font-semibold text-sm">
                                R$
                            </span>
                            <input type="text" 
                                   id="valor_total_mascara" 
                                   name="valor_total" 
                                   value="{{ old('valor_total') }}" 
                                   placeholder="0,00" 
                                   class="w-full pl-11 pr-4 bg-white dark:bg-[#02140e] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm font-semibold">
                        </div>
                        <p class="text-[11px] text-gray-400 dark:text-emerald-400/60 mt-1">Digite apenas os números, a formatação em Real é automática.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-emerald-200 mb-1">Observações / Detalhes de Acesso</label>
                        <textarea name="observacoes" rows="2" placeholder="Ex: Entrada pelos fundos, solo de grama, precisa de estacas..." class="w-full bg-white dark:bg-[#02140e] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm">{{ old('observacoes') }}</textarea>
                    </div>
                </div>

                <!-- Responsável / Solicitante -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-emerald-200 mb-1">Nome do Responsável / Solicitante</label>
                        <input type="text" name="nome_responsavel" value="{{ old('nome_responsavel') }}" placeholder="Ex: Lucas / Contato no local" class="w-full bg-white dark:bg-[#02140e] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-emerald-200 mb-1">Telefone do Responsável</label>
                        <input type="text" name="telefone_responsavel" value="{{ old('telefone_responsavel') }}" placeholder="Ex: (17) 99726-0201" class="w-full bg-white dark:bg-[#02140e] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm">
                    </div>
                </div>

                <!-- Opção de Gerar OS na Hora -->
                <div class="p-4 bg-emerald-50 dark:bg-[#04291c] rounded-lg border border-emerald-100 dark:border-[#083d2c] flex items-center gap-3">
                    <input type="checkbox" id="gerar_os_agora" name="gerar_os_agora" value="1" checked class="rounded text-emerald-600 focus:ring-amber-400 w-4 h-4 cursor-pointer">
                    <label for="gerar_os_agora" class="text-sm font-medium text-emerald-950 dark:text-emerald-200 cursor-pointer select-none">
                        <strong>Gerar Ordem de Serviço automaticamente</strong> e já notificar o montador no WhatsApp
                    </label>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-[#083d2c]">
                    <a href="{{ route('pedidos.index') }}" class="px-5 py-2.5 bg-gray-100 dark:bg-[#04291c] text-gray-700 dark:text-emerald-200 font-semibold rounded-lg hover:bg-gray-200 dark:hover:bg-[#073827] transition text-sm">Cancelar</a>
                    <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-emerald-700 to-emerald-600 hover:from-emerald-800 hover:to-emerald-700 text-amber-300 font-bold rounded-lg shadow-sm transition text-sm border border-amber-400/40">
                        Salvar Pedido
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Máscara de Moeda em Tempo Real (Real Brasileiro R$) -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const campoValor = document.getElementById('valor_total_mascara');
            if (!campoValor) return;

            function formatarMoeda(valor) {
                let limpo = valor.replace(/\D/g, '');
                if (!limpo) return '';
                let numero = (parseInt(limpo, 10) / 100).toFixed(2);
                let partes = numero.split('.');
                partes[0] = partes[0].replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                return partes.join(',');
            }

            campoValor.addEventListener('input', function (e) {
                this.value = formatarMoeda(this.value);
            });

            if (campoValor.value) {
                campoValor.value = formatarMoeda(campoValor.value);
            }
        });
    </script>
</x-app-layout>
