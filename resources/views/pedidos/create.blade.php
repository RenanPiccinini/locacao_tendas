<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 dark:text-white leading-tight">
                    Novo Pedido de Locação
                </h2>
                <p class="text-sm text-gray-500 dark:text-emerald-400/80">Defina o período, consulte o estoque em tempo real e selecione as tendas</p>
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

            <form action="{{ route('pedidos.store') }}" method="POST" class="bg-white dark:bg-[#031d14] p-6 sm:p-8 rounded-xl shadow-xs border border-gray-200 dark:border-[#083d2c] space-y-7">
                @csrf

                <!-- PASSO 1: DATAS E HORÁRIOS (PRIMEIRO INFORMA A DATA PARA VER O ESTOQUE) -->
                <div class="p-5 bg-gradient-to-r from-emerald-500/10 via-emerald-600/5 to-transparent border border-emerald-500/30 rounded-xl space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-emerald-500/20 pb-3">
                        <div class="flex items-center gap-2">
                            <span class="w-7 h-7 rounded-full bg-emerald-600 text-white font-bold flex items-center justify-center text-sm shadow-xs">1</span>
                            <h3 class="font-bold text-gray-900 dark:text-white text-base">Período da Locação & Datas</h3>
                        </div>
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 border border-emerald-500/30 flex items-center gap-1 w-fit">
                            <svg class="w-3.5 h-3.5 text-emerald-500 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Disponibilidade calculada por estas datas
                        </span>
                    </div>

                    <p class="text-xs text-gray-600 dark:text-emerald-300/80">
                        Informe as datas de instalação e devolução para o sistema verificar automaticamente quais tendas estão livres no estoque naquele intervalo:
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 pt-1">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-emerald-200 mb-1">Data Instalação *</label>
                            <input type="date" id="data_instalacao" name="data_instalacao" value="{{ old('data_instalacao', $dataInstalacao ?? date('Y-m-d')) }}" required class="w-full bg-white dark:bg-[#02140e] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm font-medium">
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
                            <input type="date" id="data_retirada" name="data_retirada" value="{{ old('data_retirada', $dataRetirada ?? date('Y-m-d', strtotime('+2 days'))) }}" required class="w-full bg-white dark:bg-[#02140e] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm font-medium">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-emerald-200 mb-1">Hora Retirada</label>
                            <input type="time" name="hora_retirada" value="{{ old('hora_retirada', '17:00') }}" class="w-full bg-white dark:bg-[#02140e] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm">
                        </div>
                    </div>

                    <div id="status-periodo-container" class="flex items-center gap-2 text-xs text-emerald-800 dark:text-emerald-300 bg-white/70 dark:bg-[#021811]/80 p-2.5 rounded-lg border border-emerald-500/20">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span id="texto-resumo-periodo">
                            Consultando estoque para: <strong>{{ \Carbon\Carbon::parse($dataInstalacao ?? date('Y-m-d'))->format('d/m/Y') }}</strong> até <strong>{{ \Carbon\Carbon::parse($dataRetirada ?? date('Y-m-d', strtotime('+2 days')))->format('d/m/Y') }}</strong>
                        </span>
                        <span id="loading-spinner" class="hidden ml-auto text-amber-500 flex items-center gap-1 font-semibold text-xs">
                            <svg class="animate-spin h-3.5 w-3.5 text-amber-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                            Atualizando tendas...
                        </span>
                    </div>
                </div>

                <!-- PASSO 2: SELEÇÃO DE TENDAS DO ESTOQUE (TODAS AS TENDAS APARECEM E ATUALIZAM POR DATA) -->
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-7 h-7 rounded-full bg-emerald-600 text-white font-bold flex items-center justify-center text-sm shadow-xs">2</span>
                            <h3 class="font-bold text-gray-900 dark:text-white text-base">Tendas para a Locação *</h3>
                        </div>
                        <span class="text-xs text-amber-600 dark:text-amber-400 font-semibold">Marque pelo menos uma tenda</span>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-emerald-400/80">
                        Veja abaixo todas as tendas do catálogo e quantas estão liberadas exatamente no período escolhido:
                    </p>

                    <div id="container-tendas" class="space-y-3">
                        @forelse($tendas as $tenda)
                            @php
                                $disp = $tenda->quantidade_disponivel_periodo ?? $tenda->quantidade_disponivel;
                                $isLiberada = $disp > 0;
                            @endphp
                            <div id="card-tenda-{{ $tenda->id }}" class="tenda-card flex flex-col sm:flex-row sm:items-center justify-between p-3.5 border {{ $isLiberada ? 'border-gray-200 dark:border-[#083d2c] hover:border-amber-400 dark:hover:border-amber-400 bg-gray-50/50 dark:bg-[#021811]' : 'border-red-200 dark:border-red-900/40 bg-red-50/30 dark:bg-red-950/20 opacity-75' }} rounded-xl transition gap-3">
                                <div class="flex items-start sm:items-center gap-3">
                                    <input type="checkbox" 
                                           id="tenda_{{ $tenda->id }}" 
                                           name="tendas_selecionadas[]" 
                                           value="{{ $tenda->id }}" 
                                           {{ in_array($tenda->id, old('tendas_selecionadas', [])) ? 'checked' : '' }}
                                           {{ !$isLiberada ? 'disabled' : '' }}
                                           onchange="toggleQuantidade({{ $tenda->id }}, this.checked);"
                                           class="tenda-checkbox rounded text-amber-500 focus:ring-amber-400 w-4 h-4 cursor-pointer mt-1 sm:mt-0">
                                    
                                    <label for="tenda_{{ $tenda->id }}" class="text-sm cursor-pointer select-none">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <span class="font-bold text-gray-900 dark:text-slate-100 text-sm">{{ $tenda->nome }}</span>
                                            <span class="text-xs text-gray-500 dark:text-gray-400">Dimensões: <strong>{{ $tenda->tamanho }}</strong></span>

                                            <!-- Badge de Disponibilidade Dinâmica -->
                                            <span id="badge-disp-{{ $tenda->id }}" class="badge-disp text-[11px] font-bold px-2 py-0.5 rounded-full {{ $isLiberada ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 border border-emerald-500/30' : 'bg-red-100 text-red-800 dark:bg-red-950 dark:text-red-300 border border-red-500/30' }}">
                                                {{ $isLiberada ? "{$disp} disponíveis neste período (Estoque total: {$tenda->quantidade_total})" : "0 disponíveis neste período (Todas as {$tenda->quantidade_total} locadas)" }}
                                            </span>
                                        </div>
                                    </label>
                                </div>

                                <div class="flex items-center gap-2 pl-7 sm:pl-0">
                                    <span class="text-xs font-semibold text-gray-600 dark:text-emerald-200">Quantidade:</span>
                                    <input type="number" 
                                           id="qtd_{{ $tenda->id }}" 
                                           name="quantidades[{{ $tenda->id }}]" 
                                           value="{{ old('quantidades.'.$tenda->id, 1) }}" 
                                           min="1" 
                                           max="{{ max(1, $disp) }}" 
                                           {{ !$isLiberada ? 'disabled' : '' }}
                                           class="tenda-qtd w-20 bg-white dark:bg-[#02140e] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm text-center font-bold">
                                </div>
                            </div>
                        @empty
                            <div class="p-6 text-center text-gray-500 dark:text-emerald-400/60 bg-gray-50 dark:bg-[#021811] rounded-xl border border-gray-200 dark:border-[#083d2c]">
                                Nenhuma tenda cadastrada no sistema. <a href="{{ route('tendas.create') }}" class="text-amber-600 dark:text-amber-400 underline font-semibold">Cadastre tendas no estoque</a>.
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- PASSO 3: CLIENTE E MONTADOR -->
                <div class="border-t border-gray-200 dark:border-[#083d2c] pt-6 space-y-4">
                    <div class="flex items-center gap-2">
                        <span class="w-7 h-7 rounded-full bg-emerald-600 text-white font-bold flex items-center justify-center text-sm shadow-xs">3</span>
                        <h3 class="font-bold text-gray-900 dark:text-white text-base">Cliente e Montador Responsável</h3>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-emerald-200 mb-1">Cliente *</label>
                            <select name="cliente_id" required class="w-full bg-white dark:bg-[#02140e] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm font-medium">
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
                            <label class="block text-sm font-semibold text-gray-700 dark:text-emerald-200 mb-1">Montador Designado</label>
                            <select name="montador_id" class="w-full bg-white dark:bg-[#02140e] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm font-medium">
                                <option value="">Selecione o montador (opcional)...</option>
                                @foreach($montadores as $m)
                                    <option value="{{ $m->id }}" {{ old('montador_id') == $m->id ? 'selected' : '' }}>
                                        {{ $m->name }} (WPP: {{ $m->whatsapp ?? 'Sem WhatsApp' }})
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-400 dark:text-emerald-400/60 mt-1">O montador selecionado receberá automaticamente a notificação no WhatsApp.</p>
                        </div>
                    </div>
                </div>

                <!-- PASSO 4: LOCAL DA MONTAGEM & DETALHES -->
                <div class="border-t border-gray-200 dark:border-[#083d2c] pt-6 space-y-4">
                    <div class="flex items-center gap-2">
                        <span class="w-7 h-7 rounded-full bg-emerald-600 text-white font-bold flex items-center justify-center text-sm shadow-xs">4</span>
                        <h3 class="font-bold text-gray-900 dark:text-white text-base">Local da Montagem & Detalhes</h3>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-emerald-200 mb-1">Endereço da Montagem *</label>
                            <input type="text" name="endereco_instalacao" value="{{ old('endereco_instalacao') }}" required placeholder="Rua, número, bairro, chácara..." class="w-full bg-white dark:bg-[#02140e] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm font-medium">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-emerald-200 mb-1">Cidade</label>
                            <input type="text" name="cidade_instalacao" value="{{ old('cidade_instalacao') }}" placeholder="Ex: Cidade / Região" class="w-full bg-white dark:bg-[#02140e] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm font-medium">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
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
                            <p class="text-[11px] text-gray-400 dark:text-emerald-400/60 mt-1">Digite o valor negociado. Se não informado, o contrato e a OS serão gerados sem valor financeiro.</p>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-emerald-200 mb-1">Observações / Detalhes de Acesso</label>
                            <textarea name="observacoes" rows="2" placeholder="Ex: Entrada pelos fundos, solo de grama, ancoragem em estacas..." class="w-full bg-white dark:bg-[#02140e] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm">{{ old('observacoes') }}</textarea>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-emerald-200 mb-1">Nome do Responsável no Local (Opcional)</label>
                            <input type="text" name="nome_responsavel" value="{{ old('nome_responsavel') }}" placeholder="Ex: Lucas / Contato no local" class="w-full bg-white dark:bg-[#02140e] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-emerald-200 mb-1">Telefone do Responsável</label>
                            <input type="text" name="telefone_responsavel" value="{{ old('telefone_responsavel') }}" placeholder="Ex: (17) 99726-0201" class="w-full bg-white dark:bg-[#02140e] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm">
                        </div>
                    </div>
                </div>

                <!-- PASSO 5: GERAÇÃO AUTOMÁTICA DA O.S. -->
                <div class="p-4 bg-emerald-50 dark:bg-[#04291c] rounded-xl border border-emerald-100 dark:border-[#083d2c] flex items-center gap-3">
                    <input type="checkbox" id="gerar_os_agora" name="gerar_os_agora" value="1" checked class="rounded text-emerald-600 focus:ring-amber-400 w-4 h-4 cursor-pointer">
                    <label for="gerar_os_agora" class="text-sm font-medium text-emerald-950 dark:text-emerald-200 cursor-pointer select-none">
                        <strong>Gerar 1ª Ordem de Serviço automaticamente</strong> e notificar o montador no WhatsApp
                    </label>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-[#083d2c]">
                    <a href="{{ route('pedidos.index') }}" class="px-5 py-2.5 bg-gray-100 dark:bg-[#04291c] text-gray-700 dark:text-emerald-200 font-semibold rounded-lg hover:bg-gray-200 dark:hover:bg-[#073827] transition text-sm">Cancelar</a>
                    <button type="submit" class="px-7 py-2.5 bg-gradient-to-r from-emerald-700 to-emerald-600 hover:from-emerald-800 hover:to-emerald-700 text-amber-300 font-bold rounded-lg shadow-sm transition text-sm border border-amber-400/40">
                        Salvar Pedido de Locação
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Script de Consulta Dinâmica de Disponibilidade de Tendas por Data -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const inputInstalacao = document.getElementById('data_instalacao');
            const inputRetirada = document.getElementById('data_retirada');
            const resumoPeriodo = document.getElementById('texto-resumo-periodo');
            const spinner = document.getElementById('loading-spinner');
            const containerTendas = document.getElementById('container-tendas');
            const campoValor = document.getElementById('valor_total_mascara');

            let timerConsulta = null;

            function formatarDataBR(dataStr) {
                if (!dataStr) return '';
                const partes = dataStr.split('-');
                if (partes.length === 3) {
                    return `${partes[2]}/${partes[1]}/${partes[0]}`;
                }
                return dataStr;
            }

            async function atualizarDisponibilidade() {
                const dtInst = inputInstalacao.value;
                const dtRet = inputRetirada.value;

                if (!dtInst || !dtRet) return;

                // Garante que retirada não seja anterior à instalação
                if (dtRet < dtInst) {
                    inputRetirada.value = dtInst;
                }

                if (spinner) spinner.classList.remove('hidden');
                if (resumoPeriodo) {
                    resumoPeriodo.innerHTML = `Consultando estoque para: <strong>${formatarDataBR(inputInstalacao.value)}</strong> até <strong>${formatarDataBR(inputRetirada.value)}</strong>...`;
                }

                try {
                    const url = `{{ route('pedidos.disponibilidade-tendas') }}?data_instalacao=${encodeURIComponent(inputInstalacao.value)}&data_retirada=${encodeURIComponent(inputRetirada.value)}`;
                    const res = await fetch(url, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!res.ok) throw new Error('Falha na resposta');

                    const data = await res.json();

                    if (data.success && Array.isArray(data.tendas)) {
                        if (resumoPeriodo) {
                            resumoPeriodo.innerHTML = `Período selecionado: <strong>${data.data_formatada}</strong> • Total disponível: <strong>${data.total_disponivel} tendas liberadas</strong>`;
                        }

                        data.tendas.forEach(tenda => {
                            const card = document.getElementById(`card-tenda-${tenda.id}`);
                            const checkbox = document.getElementById(`tenda_${tenda.id}`);
                            const badge = document.getElementById(`badge-disp-${tenda.id}`);
                            const inputQtd = document.getElementById(`qtd_${tenda.id}`);

                            if (!card) return;

                            const disp = tenda.quantidade_disponivel;
                            const isLiberada = disp > 0;

                            // Atualiza badge
                            if (badge) {
                                if (isLiberada) {
                                    badge.className = 'badge-disp text-[11px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 border border-emerald-500/30';
                                    badge.textContent = `${disp} disponíveis neste período (Estoque total: ${tenda.quantidade_total})`;
                                } else {
                                    badge.className = 'badge-disp text-[11px] font-bold px-2 py-0.5 rounded-full bg-red-100 text-red-800 dark:bg-red-950 dark:text-red-300 border border-red-500/30';
                                    badge.textContent = `0 disponíveis neste período (Todas as ${tenda.quantidade_total} locadas)`;
                                }
                            }

                            // Atualiza checkbox e card
                            if (checkbox) {
                                if (isLiberada) {
                                    checkbox.disabled = false;
                                    card.className = 'tenda-card flex flex-col sm:flex-row sm:items-center justify-between p-3.5 border border-gray-200 dark:border-[#083d2c] hover:border-amber-400 dark:hover:border-amber-400 bg-gray-50/50 dark:bg-[#021811] rounded-xl transition gap-3';
                                } else {
                                    checkbox.checked = false;
                                    checkbox.disabled = true;
                                    card.className = 'tenda-card flex flex-col sm:flex-row sm:items-center justify-between p-3.5 border border-red-200 dark:border-red-900/40 bg-red-50/30 dark:bg-red-950/20 opacity-75 rounded-xl transition gap-3';
                                }
                            }

                            // Atualiza input de quantidade
                            if (inputQtd) {
                                inputQtd.max = Math.max(1, disp);
                                if (!isLiberada) {
                                    inputQtd.disabled = true;
                                    inputQtd.value = 1;
                                } else {
                                    if (parseInt(inputQtd.value, 10) > disp) {
                                        inputQtd.value = disp;
                                    }
                                }
                            }
                        });
                    }
                } catch (err) {
                    console.error('Erro ao consultar disponibilidade:', err);
                } finally {
                    if (spinner) spinner.classList.add('hidden');
                }
            }

            // Dispara verificação quando qualquer data é alterada
            function agendarConsulta() {
                clearTimeout(timerConsulta);
                timerConsulta = setTimeout(atualizarDisponibilidade, 300);
            }

            inputInstalacao.addEventListener('change', agendarConsulta);
            inputRetirada.addEventListener('change', agendarConsulta);

            // Função global para alternar input de quantidade
            window.toggleQuantidade = function (tendaId, isChecked) {
                const inputQtd = document.getElementById(`qtd_${tendaId}`);
                if (inputQtd) {
                    inputQtd.disabled = !isChecked;
                }
            };

            // Formatação de Moeda
            function formatarMoeda(valor) {
                let limpo = valor.replace(/\D/g, '');
                if (!limpo) return '';
                let numero = (parseInt(limpo, 10) / 100).toFixed(2);
                let partes = numero.split('.');
                partes[0] = partes[0].replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                return partes.join(',');
            }

            if (campoValor) {
                campoValor.addEventListener('input', function () {
                    this.value = formatarMoeda(this.value);
                });
                if (campoValor.value) {
                    campoValor.value = formatarMoeda(campoValor.value);
                }
            }
        });
    </script>
</x-app-layout>
