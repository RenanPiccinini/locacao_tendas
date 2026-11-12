<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 dark:text-white leading-tight">
                    Conexão com WhatsApp (Evolution API)
                </h2>
                <p class="text-sm text-gray-500 dark:text-emerald-300/70">Conecte sua instância da Hostinger para envio automático de Ordens de Serviço e lembretes</p>
            </div>
            <a href="https://evolution.piccininidigitalsolutions.com.br/manager/" target="_blank" class="w-fit px-3 py-1.5 bg-gray-100 dark:bg-[#063325] hover:bg-gray-200 dark:hover:bg-[#0a4835] text-gray-700 dark:text-emerald-100 text-xs font-semibold rounded-lg transition flex items-center gap-1.5 border border-transparent dark:border-emerald-700/40">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                Painel Evolution Hostinger
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Card de Status e Instância -->
            <div class="bg-white dark:bg-[#031d14] p-6 rounded-xl shadow-sm border border-gray-100 dark:border-[#083d2c] transition-colors">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-gray-100 dark:border-[#083d2c]">
                    <div>
                        <span class="text-xs font-semibold text-gray-400 dark:text-emerald-400/70 uppercase tracking-wider">Instância Configurada</span>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mt-0.5">{{ $instance }}</h3>
                    </div>

                    <div id="status-container" class="flex items-center gap-3">
                        <span id="badge-status" class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold bg-gray-100 dark:bg-[#063325] text-gray-700 dark:text-emerald-200 border border-transparent dark:border-emerald-700/40">
                            <span class="w-2 h-2 rounded-full bg-gray-400 me-2 animate-pulse"></span>
                            Consultando status...
                        </span>

                        <button id="btn-atualizar-status" class="p-2 text-gray-500 dark:text-emerald-300 hover:text-gray-700 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-[#063325] rounded-lg transition" title="Recarregar status">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        </button>
                    </div>
                </div>

                <!-- Painel Informativo Conectado / Desconectado -->
                <div class="pt-6">
                    <!-- Se Conectado -->
                    <div id="painel-conectado" class="hidden text-center py-6">
                        <div class="w-16 h-16 bg-emerald-100 dark:bg-emerald-950/70 text-emerald-600 dark:text-emerald-400 rounded-full flex items-center justify-center mx-auto mb-3 border border-emerald-200 dark:border-emerald-700/50">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <h4 class="text-lg font-bold text-gray-900 dark:text-white">WhatsApp Conectado e Operacional!</h4>
                        <p class="text-sm text-gray-500 dark:text-emerald-300/70 max-w-md mx-auto mt-1">
                            Sua instância está pronta para enviar ordens de serviço aos montadores, fotos e lembretes automáticos toda segunda-feira e no dia das montagens.
                        </p>

                        <div class="mt-6 flex justify-center gap-3">
                            <button id="btn-desconectar" class="px-4 py-2 border border-red-300 dark:border-red-800/60 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/40 text-xs font-bold rounded-lg transition">
                                Desconectar Sessão
                            </button>
                        </div>
                    </div>

                    <!-- Se Desconectado (Exibe QR Code) -->
                    <div id="painel-desconectado" class="hidden text-center py-6">
                        <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Conecte o WhatsApp escaneando o QR Code abaixo:</h4>
                        <p class="text-xs text-gray-500 dark:text-emerald-300/70 mb-6">Abra o WhatsApp no celular &gt; Menu (3 pontinhos ou Ajustes) &gt; Aparelhos Conectados &gt; Conectar Aparelho</p>

                        <div class="inline-block p-4 bg-white dark:bg-white border-2 border-gray-200 dark:border-amber-400/40 rounded-2xl shadow-inner relative">
                            <div id="qrcode-loading" class="w-64 h-64 flex items-center justify-center bg-gray-50 text-gray-400 text-sm">
                                Carregando QR Code...
                            </div>
                            <img id="qrcode-img" src="" alt="QR Code WhatsApp" class="w-64 h-64 mx-auto hidden object-contain">
                        </div>

                        <div class="mt-4">
                            <button id="btn-gerar-qrcode" class="px-5 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white font-bold rounded-lg text-xs shadow transition">
                                Gerar Novo QR Code
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card de Lembretes Automáticos e Cron Jobs da Hostinger -->
            <div class="bg-white dark:bg-[#031d14] p-6 rounded-xl shadow-sm border border-gray-100 dark:border-[#083d2c] space-y-4 transition-colors">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-gray-100 dark:border-[#083d2c] pb-4">
                    <div>
                        <h4 class="font-bold text-gray-900 dark:text-white text-base flex items-center gap-2">
                            <span class="text-xl">⏰</span>
                            Lembretes Automáticos aos Montadores (WhatsApp)
                        </h4>
                        <p class="text-xs text-gray-500 dark:text-emerald-300/70 mt-0.5">Disparos programados executados automaticamente pelo sistema</p>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <button type="button" id="btn-teste-semana" class="px-3.5 py-2 bg-indigo-50 dark:bg-indigo-950/60 hover:bg-indigo-100 dark:hover:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300 font-bold text-xs rounded-lg transition border border-indigo-200 dark:border-indigo-800/40 flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/></svg>
                            Testar Lembrete Semanal
                        </button>

                        <button type="button" id="btn-teste-dia" class="px-3.5 py-2 bg-emerald-50 dark:bg-emerald-950/60 hover:bg-emerald-100 dark:hover:bg-emerald-900/50 text-emerald-700 dark:text-emerald-300 font-bold text-xs rounded-lg transition border border-emerald-200 dark:border-emerald-800/40 flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/></svg>
                            Testar Lembrete Diário (Hoje)
                        </button>
                    </div>
                </div>

                <div id="resultado-teste-cron" class="hidden p-3 rounded-lg text-xs font-mono bg-gray-900 text-emerald-400 overflow-x-auto"></div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                    <div class="p-3.5 bg-gray-50 dark:bg-[#02140e] rounded-lg border border-gray-200 dark:border-[#083d2c]">
                        <div class="font-bold text-gray-800 dark:text-white text-sm mb-1 flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
                            Toda Segunda-feira às 07h00
                        </div>
                        <p class="text-gray-600 dark:text-emerald-200/70">
                            Dispara para cada montador a <strong>agenda completa da semana</strong> contendo todas as instalações agendadas de segunda a domingo destinadas a ele.
                        </p>
                    </div>

                    <div class="p-3.5 bg-gray-50 dark:bg-[#02140e] rounded-lg border border-gray-200 dark:border-[#083d2c]">
                        <div class="font-bold text-gray-800 dark:text-white text-sm mb-1 flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span>
                            Terça a Domingo às 07h00
                        </div>
                        <p class="text-gray-600 dark:text-emerald-200/70">
                            Dispara para cada montador <strong>somente as instalações do dia atual</strong> destinadas a ele (na segunda-feira não repete, pois o semanal já envia toda a semana).
                        </p>
                    </div>
                </div>

                <!-- Guia de Configuração Hostinger -->
                <div class="mt-2 pt-3 border-t border-gray-100 dark:border-[#083d2c]">
                    <details class="text-xs text-gray-600 dark:text-emerald-200/70">
                        <summary class="font-bold text-gray-800 dark:text-emerald-300 cursor-pointer hover:text-emerald-600 dark:hover:text-amber-400 flex items-center gap-1.5 select-none">
                            <svg class="w-4 h-4 text-gray-500 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Como configurar o Cron Job no painel da Hostinger (Clique para ver)
                        </summary>
                        <div class="mt-3 p-3.5 bg-slate-900 text-slate-200 rounded-lg space-y-2">
                            <p>No <strong>hPanel da Hostinger &gt; Cron Jobs</strong>, crie a tarefa a cada minuto (<code>* * * * *</code>) com o comando:</p>
                            <code class="block p-2 bg-black/60 rounded text-emerald-400 font-mono select-all">
                                cd /home/u639274314/domains/tendaseldorado.com.br/public_html &amp;&amp; php artisan schedule:run &gt;&gt; /dev/null 2&gt;&amp;1
                            </code>
                            <p class="text-gray-400">Ou utilize a URL do Web Cron (recomendado para garantir PHP 8.3+):</p>
                            <code class="block p-2 bg-black/60 rounded text-emerald-400 font-mono select-all">
                                curl -s -L "https://tendaseldorado.com.br/cron/scheduler" &gt;/dev/null 2&gt;&amp;1
                            </code>
                        </div>
                    </details>
                </div>
            </div>

        </div>
    </div>

    <script>
        const badgeStatus = document.getElementById('badge-status');
        const painelConectado = document.getElementById('painel-conectado');
        const painelDesconectado = document.getElementById('painel-desconectado');
        const qrcodeImg = document.getElementById('qrcode-img');
        const qrcodeLoading = document.getElementById('qrcode-loading');
        const btnAtualizar = document.getElementById('btn-atualizar-status');
        const btnGerarQrcode = document.getElementById('btn-gerar-qrcode');
        const btnDesconectar = document.getElementById('btn-desconectar');

        let pollingInterval = null;
        let isChecking = false;
        let estavaAguardandoConexao = false;

        function definirEstadoConectado() {
            badgeStatus.className = 'inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800';
            badgeStatus.innerHTML = '<span class="w-2 h-2 rounded-full bg-emerald-500 me-2"></span> Conectado';
            painelConectado.classList.remove('hidden');
            painelDesconectado.classList.add('hidden');
        }

        function definirEstadoDesconectado() {
            badgeStatus.className = 'inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold bg-red-100 text-red-800';
            badgeStatus.innerHTML = '<span class="w-2 h-2 rounded-full bg-red-500 me-2"></span> Desconectado';
            painelConectado.classList.add('hidden');
            painelDesconectado.classList.remove('hidden');
        }

        function iniciarPolling() {
            if (pollingInterval) return;
            pollingInterval = setInterval(async () => {
                if (isChecking) return;
                isChecking = true;
                try {
                    await verificarStatus(false);
                } finally {
                    isChecking = false;
                }
            }, 3000);
        }

        function pararPolling() {
            if (pollingInterval) {
                clearInterval(pollingInterval);
                pollingInterval = null;
            }
        }

        async function verificarStatus(isInitial = false) {
            try {
                const res = await fetch('{{ route("whatsapp.status") }}');
                const data = await res.json();

                const state = data?.instance?.state || data?.state || 'close';

                if (state === 'open' || state === 'connected') {
                    pararPolling();
                    definirEstadoConectado();

                    if (estavaAguardandoConexao) {
                        estavaAguardandoConexao = false;
                        window.location.reload();
                        return;
                    }
                } else {
                    estavaAguardandoConexao = true;
                    definirEstadoDesconectado();
                    if (isInitial) {
                        carregarQrCode();
                    }
                    iniciarPolling();
                }
            } catch (e) {
                badgeStatus.className = 'inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800';
                badgeStatus.innerHTML = 'Verificando conexão...';
                painelDesconectado.classList.remove('hidden');
            }
        }

        async function carregarQrCode() {
            qrcodeLoading.classList.remove('hidden');
            qrcodeImg.classList.add('hidden');

            try {
                const res = await fetch('{{ route("whatsapp.qrcode") }}');
                const data = await res.json();

                const base64 = data?.base64 || data?.qrcode?.base64;
                if (base64) {
                    qrcodeImg.src = base64;
                    qrcodeImg.classList.remove('hidden');
                    qrcodeLoading.classList.add('hidden');
                } else if (data?.code) {
                    qrcodeLoading.textContent = 'Aguardando leitura do QR Code...';
                } else {
                    qrcodeLoading.textContent = 'Não foi possível gerar QR Code no momento.';
                }
            } catch (e) {
                qrcodeLoading.textContent = 'Erro ao buscar QR Code.';
            }
        }

        if (btnAtualizar) {
            btnAtualizar.addEventListener('click', () => {
                badgeStatus.className = 'inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold bg-gray-100 text-gray-700';
                badgeStatus.innerHTML = '<span class="w-2 h-2 rounded-full bg-gray-400 me-2 animate-pulse"></span> Consultando status...';
                verificarStatus(false);
            });
        }

        if (btnGerarQrcode) {
            btnGerarQrcode.addEventListener('click', () => {
                carregarQrCode();
                iniciarPolling();
            });
        }

        if (btnDesconectar) {
            btnDesconectar.addEventListener('click', async () => {
                const confirmou = await window.appConfirm(
                    'Desconectar e Excluir Instância',
                    'Deseja realmente desconectar e <strong>DELETAR</strong> a instância <strong>{{ $instance }}</strong> na Evolution API?<br><br><span class="text-xs text-amber-500 font-semibold">⚠️ Atenção: Apenas a instância {{ $instance }} será excluída, nenhuma outra será afetada.</span>',
                    {
                        type: 'danger',
                        confirmText: 'Sim, Desconectar e Deletar',
                        cancelText: 'Cancelar'
                    }
                );

                if (!confirmou) return;

                badgeStatus.className = 'inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold bg-amber-100 dark:bg-amber-950/40 text-amber-800 dark:text-amber-300';
                badgeStatus.innerHTML = '<span class="w-2 h-2 rounded-full bg-amber-500 me-2 animate-pulse"></span> Desconectando e removendo instância...';

                try {
                    const res = await fetch('{{ route("whatsapp.desconectar") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    });
                    const data = await res.json();
                    if (data?.message) {
                        await window.appAlert(
                            data.success ? 'Instância Desconectada' : 'Aviso',
                            data.message,
                            data.success ? 'success' : 'warning'
                        );
                    }
                } catch (e) {
                    console.error('Erro ao desconectar:', e);
                    await window.appAlert('Erro', 'Ocorreu um erro ao tentar desconectar a instância.', 'danger');
                }
                verificarStatus(true);
            });
        }

        // Teste de disparo de lembretes manuais
        const btnTesteSemana = document.getElementById('btn-teste-semana');
        const btnTesteDia = document.getElementById('btn-teste-dia');
        const boxResultadoCron = document.getElementById('resultado-teste-cron');

        async function dispararTesteLembrete(tipo, botao) {
            const textoOriginal = botao.innerHTML;
            botao.disabled = true;
            botao.innerHTML = '<span class="animate-pulse">Disparando...</span>';

            try {
                const res = await fetch('{{ route("whatsapp.testar-lembrete") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ tipo: tipo })
                });

                const data = await res.json();
                boxResultadoCron.classList.remove('hidden');
                boxResultadoCron.innerHTML = `<strong>[Retorno ${tipo.toUpperCase()}]:</strong>\n${data.output || 'Executado com sucesso!'}`;
            } catch (e) {
                boxResultadoCron.classList.remove('hidden');
                boxResultadoCron.innerHTML = `<span class="text-red-400">Erro ao executar disparo: ${e.message}</span>`;
            } finally {
                botao.disabled = false;
                botao.innerHTML = textoOriginal;
            }
        }

        if (btnTesteSemana) {
            btnTesteSemana.addEventListener('click', () => dispararTesteLembrete('semana', btnTesteSemana));
        }

        if (btnTesteDia) {
            btnTesteDia.addEventListener('click', () => dispararTesteLembrete('dia', btnTesteDia));
        }

        // Executa ao abrir
        verificarStatus(true);
    </script>
</x-app-layout>
