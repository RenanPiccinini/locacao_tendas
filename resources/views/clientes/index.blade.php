<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 dark:text-white leading-tight">Clientes</h2>
                <p class="text-sm text-gray-500 dark:text-emerald-300/70">Gestão de contatos, endereços e contratos de locação</p>
            </div>
            <a href="{{ route('clientes.create') }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-emerald-700 to-emerald-600 hover:from-emerald-800 hover:to-emerald-700 text-amber-300 font-bold rounded-lg shadow-sm text-sm border border-amber-400/40 transition">
                + Novo Cliente
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white dark:bg-[#031d14] rounded-xl shadow-xs border border-gray-200 dark:border-[#083d2c] overflow-hidden transition-colors">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-600 dark:text-emerald-100/90 min-w-[700px]">
                        <thead class="bg-gray-50 dark:bg-[#02140e] text-xs uppercase font-semibold text-gray-700 dark:text-emerald-300 border-b border-gray-100 dark:border-[#083d2c]">
                            <tr>
                                <th class="px-6 py-4">Nome</th>
                                <th class="px-6 py-4">Telefone / WhatsApp</th>
                                <th class="px-6 py-4">Endereço Principal</th>
                                <th class="px-6 py-4">Locações</th>
                                <th class="px-6 py-4 text-right">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-[#083d2c]">
                            @forelse($clientes as $cliente)
                                <tr class="hover:bg-gray-50 dark:hover:bg-[#063325] transition" id="cliente-row-{{ $cliente->id }}">
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-gray-900 dark:text-white">{{ $cliente->nome }}</div>
                                        <div class="text-xs text-gray-400 dark:text-emerald-400/60">{{ $cliente->email }}</div>
                                    </td>
                                    <td class="px-6 py-4 font-semibold text-emerald-600 dark:text-amber-400">{{ $cliente->telefone }}</td>
                                    <td class="px-6 py-4 text-gray-700 dark:text-emerald-100">{{ $cliente->endereco }} {{ $cliente->cidade ? " - {$cliente->cidade}" : '' }}</td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-gray-100 dark:bg-[#02140e] text-gray-800 dark:text-emerald-200 border border-transparent dark:border-emerald-700/40">
                                            {{ $cliente->pedidos_count }} pedidos
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                                        <!-- Botão Dinâmico de Contrato (Gerar / Ver Contrato) -->
                                        <span id="contrato-container-{{ $cliente->id }}" class="inline-block mr-1">
                                            @if($cliente->temContrato())
                                                <a href="{{ route('clientes.contrato.ver', $cliente) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-amber-500/15 text-amber-600 dark:text-amber-300 border border-amber-500/30 hover:bg-amber-500/25 transition" title="Abrir e baixar PDF do contrato">
                                                    <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                    Ver Contrato
                                                </a>
                                                <button type="button" onclick="gerarContrato({{ $cliente->id }}, this, '{{ addslashes($cliente->nome) }}')" title="Regerar / Atualizar Contrato" class="inline-flex items-center p-1.5 rounded-lg text-gray-400 hover:text-amber-500 dark:hover:text-amber-300 transition">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                    </svg>
                                                </button>
                                            @else
                                                <button type="button" onclick="gerarContrato({{ $cliente->id }}, this, '{{ addslashes($cliente->nome) }}')" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-gradient-to-r from-emerald-700 to-emerald-600 hover:from-emerald-800 hover:to-emerald-700 text-amber-300 border border-amber-400/40 shadow-xs transition" title="Gerar contrato em PDF com os dados deste cliente">
                                                    <svg class="w-4 h-4 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                    </svg>
                                                    Gerar Contrato
                                                </button>
                                            @endif
                                        </span>

                                        <a href="{{ route('clientes.edit', $cliente) }}" class="text-xs text-emerald-700 dark:text-emerald-400 hover:underline font-semibold">Editar</a>
                                        
                                        <!-- Exclusão com Modal Bonita Sem Alert/Confirm Nativo -->
                                        <form action="{{ route('clientes.destroy', $cliente) }}" method="POST" class="inline" 
                                              data-confirm="Deseja realmente excluir o cliente <strong>{{ $cliente->nome }}</strong>?<br><span class='text-xs text-red-500'>Esta ação removerá o registro do sistema.</span>" 
                                              data-confirm-title="Excluir Cliente" 
                                              data-confirm-btn="Sim, Excluir"
                                              data-confirm-type="danger">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs text-red-600 dark:text-red-400 hover:underline font-semibold">Excluir</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-gray-500 dark:text-emerald-300/60">Nenhum cliente cadastrado ainda.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Script de Geração Dinâmica de Contrato -->
    <script>
        async function gerarContrato(clienteId, btn, clienteNome) {
            const container = document.getElementById(`contrato-container-${clienteId}`);
            const originalHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = `
                <svg class="animate-spin -ml-1 mr-1.5 h-3.5 w-3.5 text-amber-300" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Gerando PDF...
            `;

            try {
                const res = await fetch(`/clientes/${clienteId}/gerar-contrato`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });

                const data = await res.json();

                if (data.success) {
                    // Atualiza o botão para "Ver Contrato" + botão de regerar
                    container.innerHTML = `
                        <a href="${data.url}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-amber-500/15 text-amber-600 dark:text-amber-300 border border-amber-500/30 hover:bg-amber-500/25 transition" title="Abrir e baixar PDF do contrato">
                            <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            Ver Contrato
                        </a>
                        <button type="button" onclick="gerarContrato(${clienteId}, this, '${clienteNome}')" title="Regerar / Atualizar Contrato" class="inline-flex items-center p-1.5 rounded-lg text-gray-400 hover:text-amber-500 dark:hover:text-amber-300 transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                        </button>
                    `;

                    // Exibe a modal elegante de confirmação e acesso imediato ao PDF
                    const abrirPdf = await window.appModal({
                        title: 'Contrato Gerado com Sucesso!',
                        message: `O contrato de <strong>${clienteNome}</strong> foi gerado e preenchido dinamicamente com os dados cadastrais e de locação.<br><br>Deseja visualizar e baixar o documento agora?`,
                        type: 'success',
                        confirmText: '👁️ Abrir PDF do Contrato',
                        cancelText: 'Fechar',
                        showCancel: true
                    });

                    if (abrirPdf) {
                        window.open(data.url, '_blank');
                    }
                } else {
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                    await window.appAlert('Erro', data.message || 'Falha ao gerar o contrato.', 'danger');
                }
            } catch (err) {
                btn.disabled = false;
                btn.innerHTML = originalHtml;
                console.error(err);
                await window.appAlert('Erro', 'Ocorreu um erro na requisição para gerar o contrato.', 'danger');
            }
        }
    </script>
</x-app-layout>
