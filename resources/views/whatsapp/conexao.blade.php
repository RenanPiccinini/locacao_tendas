<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    Conexão com WhatsApp (Evolution API)
                </h2>
                <p class="text-sm text-gray-500">Conecte sua instância da Hostinger para envio automático de Ordens de Serviço e lembretes</p>
            </div>
            <a href="https://evolution.piccinidigitalsolutions.com.br/manager/" target="_blank" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                Painel Evolution Hostinger
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Card de Status e Instância -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-gray-100">
                    <div>
                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Instância Configurada</span>
                        <h3 class="text-xl font-bold text-gray-900 mt-0.5">{{ $instance }}</h3>
                        <p class="text-xs text-gray-500 mt-1">Servidor VPS: <code class="text-blue-600 bg-blue-50 px-2 py-0.5 rounded">{{ $apiUrl }}</code></p>
                    </div>

                    <div id="status-container" class="flex items-center gap-3">
                        <span id="badge-status" class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold bg-gray-100 text-gray-700">
                            <span class="w-2 h-2 rounded-full bg-gray-400 me-2 animate-pulse"></span>
                            Consultando status...
                        </span>

                        <button id="btn-atualizar-status" class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition" title="Recarregar status">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        </button>
                    </div>
                </div>

                <!-- Painel Informativo Conectado / Desconectado -->
                <div class="pt-6">
                    <!-- Se Conectado -->
                    <div id="painel-conectado" class="hidden text-center py-6">
                        <div class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <h4 class="text-lg font-bold text-gray-900">WhatsApp Conectado e Operacional!</h4>
                        <p class="text-sm text-gray-500 max-w-md mx-auto mt-1">
                            Sua instância está pronta para enviar ordens de serviço aos montadores, fotos e lembretes automáticos toda segunda-feira e no dia das montagens.
                        </p>

                        <div class="mt-6 flex justify-center gap-3">
                            <button id="btn-desconectar" class="px-4 py-2 border border-red-300 text-red-600 hover:bg-red-50 text-xs font-bold rounded-lg transition">
                                Desconectar Sessão
                            </button>
                        </div>
                    </div>

                    <!-- Se Desconectado (Exibe QR Code) -->
                    <div id="painel-desconectado" class="hidden text-center py-6">
                        <h4 class="text-lg font-bold text-gray-900 mb-2">Conecte o WhatsApp escaneando o QR Code abaixo:</h4>
                        <p class="text-xs text-gray-500 mb-6">Abra o WhatsApp no celular &gt; Menu (3 pontinhos ou Ajustes) &gt; Aparelhos Conectados &gt; Conectar Aparelho</p>

                        <div class="inline-block p-4 bg-white border-2 border-gray-200 rounded-2xl shadow-inner relative">
                            <div id="qrcode-loading" class="w-64 h-64 flex items-center justify-center bg-gray-50 text-gray-400 text-sm">
                                Carregando QR Code...
                            </div>
                            <img id="qrcode-img" src="" alt="QR Code WhatsApp" class="w-64 h-64 mx-auto hidden object-contain">
                        </div>

                        <div class="mt-4">
                            <button id="btn-gerar-qrcode" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg text-xs shadow transition">
                                Gerar Novo QR Code
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Guia de Configuração .env -->
            <div class="bg-gray-50 p-6 rounded-xl border border-gray-200">
                <h4 class="font-bold text-gray-900 text-sm mb-2">💡 Dica de Configuração do .env</h4>
                <p class="text-xs text-gray-600 mb-3">
                    Certifique-se de que a variável <code>EVOLUTION_API_KEY</code> esteja com a chave correta da sua VPS Hostinger.
                </p>
                <div class="bg-gray-900 text-gray-200 p-3 rounded-lg text-xs font-mono overflow-x-auto">
                    EVOLUTION_API_URL=https://evolution.piccinidigitalsolutions.com.br<br>
                    EVOLUTION_API_KEY=Sua_Chave_De_Api_Aqui<br>
                    EVOLUTION_INSTANCE=PDS
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

        async function verificarStatus() {
            try {
                const res = await fetch('{{ route("whatsapp.status") }}');
                const data = await res.json();

                const state = data?.instance?.state || data?.state || 'close';

                if (state === 'open' || state === 'connected') {
                    badgeStatus.className = 'inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800';
                    badgeStatus.innerHTML = '<span class="w-2 h-2 rounded-full bg-emerald-500 me-2"></span> Conectado';
                    painelConectado.classList.remove('hidden');
                    painelDesconectado.classList.add('hidden');
                } else {
                    badgeStatus.className = 'inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold bg-red-100 text-red-800';
                    badgeStatus.innerHTML = '<span class="w-2 h-2 rounded-full bg-red-500 me-2"></span> Desconectado';
                    painelConectado.classList.add('hidden');
                    painelDesconectado.classList.remove('hidden');
                    carregarQrCode();
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

        if (btnAtualizar) btnAtualizar.addEventListener('click', verificarStatus);
        if (btnGerarQrcode) btnGerarQrcode.addEventListener('click', carregarQrCode);

        if (btnDesconectar) {
            btnDesconectar.addEventListener('click', async () => {
                if (confirm('Deseja realmente desconectar a sessão do WhatsApp?')) {
                    await fetch('{{ route("whatsapp.desconectar") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    });
                    verificarStatus();
                }
            });
        }

        // Executa ao abrir
        verificarStatus();
    </script>
</x-app-layout>

