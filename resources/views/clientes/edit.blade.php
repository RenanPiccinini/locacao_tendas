<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-2xl text-gray-800 dark:text-white leading-tight">Editar Cliente: {{ $cliente->nome }}</h2>
            <a href="{{ route('clientes.index') }}" class="text-sm font-semibold text-emerald-700 dark:text-amber-400 hover:text-emerald-900 dark:hover:text-amber-300">&larr; Voltar aos Clientes</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <form action="{{ route('clientes.update', $cliente) }}" method="POST" class="bg-white dark:bg-[#031d14] p-6 rounded-xl shadow-sm border border-gray-100 dark:border-[#083d2c] space-y-5">
                @csrf
                @method('PUT')

                <!-- Dados Principais -->
                <div class="space-y-4">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-emerald-700 dark:text-amber-400 border-b border-gray-100 dark:border-[#083d2c] pb-2">
                        Dados Principais
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-emerald-200 mb-1">Nome Completo / Razão Social *</label>
                            <input type="text" name="nome" value="{{ old('nome', $cliente->nome) }}" required class="w-full bg-white dark:bg-[#02140e] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-emerald-200 mb-1">Telefone / WhatsApp *</label>
                            <input type="text" name="telefone" value="{{ old('telefone', $cliente->telefone) }}" required class="w-full bg-white dark:bg-[#02140e] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-emerald-200 mb-1">E-mail</label>
                            <input type="email" name="email" value="{{ old('email', $cliente->email) }}" class="w-full bg-white dark:bg-[#02140e] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-emerald-200 mb-1">CPF / CNPJ</label>
                            <input type="text" name="cpf_cnpj" value="{{ old('cpf_cnpj', $cliente->cpf_cnpj) }}" class="w-full bg-white dark:bg-[#02140e] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm">
                        </div>
                    </div>
                </div>

                <!-- Endereço com CEP Primeiro e Busca Automática -->
                <div class="space-y-4 pt-2">
                    <div class="flex items-center justify-between border-b border-gray-100 dark:border-[#083d2c] pb-2">
                        <h3 class="text-sm font-bold uppercase tracking-wider text-emerald-700 dark:text-amber-400">
                            Endereço
                        </h3>
                        <span class="text-[11px] text-gray-400 dark:text-emerald-300/70">Digite o CEP para buscar automaticamente</span>
                    </div>

                    <!-- 1º CAMPO: CEP -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="relative">
                            <label for="cep" class="block text-sm font-semibold text-gray-700 dark:text-emerald-200 mb-1">CEP *</label>
                            <div class="relative">
                                <input type="text" id="cep" name="cep" value="{{ old('cep', $cliente->cep) }}" maxlength="9" placeholder="00000-000" onkeyup="aplicarMascaraCep(this)" onblur="buscarCep(this.value)" class="w-full bg-white dark:bg-[#02140e] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm pr-9">
                                <div id="cep-spinner" class="hidden absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <svg class="animate-spin h-4 w-4 text-emerald-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div id="cep-feedback" class="text-xs mt-1"></div>
                        </div>

                        <div class="sm:col-span-2 flex items-end pb-1">
                            <button type="button" onclick="buscarCep(document.getElementById('cep').value)" class="px-3 py-2 bg-emerald-50 dark:bg-[#063325] text-emerald-700 dark:text-emerald-300 hover:bg-emerald-100 dark:hover:bg-[#084532] border border-emerald-200 dark:border-emerald-700/50 rounded-lg text-xs font-semibold transition flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                Buscar CEP
                            </button>
                        </div>
                    </div>

                    <!-- RUA/AVENIDA & NÚMERO -->
                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                        <div class="sm:col-span-3">
                            <label for="endereco" class="block text-sm font-semibold text-gray-700 dark:text-emerald-200 mb-1">Rua / Avenida *</label>
                            <input type="text" id="endereco" name="endereco" value="{{ old('endereco', $cliente->endereco) }}" required placeholder="Ex: Av. Alberto Andaló" class="w-full bg-white dark:bg-[#02140e] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm">
                        </div>
                        <div>
                            <label for="numero" class="block text-sm font-semibold text-gray-700 dark:text-emerald-200 mb-1">Número</label>
                            <input type="text" id="numero" name="numero" value="{{ old('numero', $cliente->numero) }}" placeholder="Ex: 1500" class="w-full bg-white dark:bg-[#02140e] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm">
                        </div>
                    </div>

                    <!-- COMPLEMENTO & BAIRRO -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="complemento" class="block text-sm font-semibold text-gray-700 dark:text-emerald-200 mb-1">Complemento</label>
                            <input type="text" id="complemento" name="complemento" value="{{ old('complemento', $cliente->complemento) }}" placeholder="Ex: Apto 101, Bloco B, Galpão" class="w-full bg-white dark:bg-[#02140e] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm">
                        </div>
                        <div>
                            <label for="bairro" class="block text-sm font-semibold text-gray-700 dark:text-emerald-200 mb-1">Bairro</label>
                            <input type="text" id="bairro" name="bairro" value="{{ old('bairro', $cliente->bairro) }}" placeholder="Ex: Centro" class="w-full bg-white dark:bg-[#02140e] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm">
                        </div>
                    </div>

                    <!-- CIDADE & ESTADO -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="sm:col-span-2">
                            <label for="cidade" class="block text-sm font-semibold text-gray-700 dark:text-emerald-200 mb-1">Cidade</label>
                            <input type="text" id="cidade" name="cidade" value="{{ old('cidade', $cliente->cidade) }}" placeholder="Ex: Cidade" class="w-full bg-white dark:bg-[#02140e] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm">
                        </div>
                        <div>
                            <label for="estado" class="block text-sm font-semibold text-gray-700 dark:text-emerald-200 mb-1">Estado (UF)</label>
                            <input type="text" id="estado" name="estado" value="{{ old('estado', $cliente->estado) }}" maxlength="2" placeholder="Ex: SP" class="w-full uppercase bg-white dark:bg-[#02140e] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm">
                        </div>
                    </div>
                </div>

                <!-- Observações -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-emerald-200 mb-1">Observações</label>
                    <textarea name="observacoes" rows="2" placeholder="Informações de acesso, referências ou anotações..." class="w-full bg-white dark:bg-[#02140e] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm">{{ old('observacoes', $cliente->observacoes) }}</textarea>
                </div>

                <div class="flex justify-end gap-2 pt-2 border-t border-gray-100 dark:border-[#083d2c]">
                    <a href="{{ route('clientes.index') }}" class="px-4 py-2 bg-gray-100 dark:bg-[#04291c] text-gray-700 dark:text-emerald-200 rounded-lg text-sm font-semibold hover:bg-gray-200 dark:hover:bg-[#073827]">Cancelar</a>
                    <button type="submit" class="px-5 py-2 bg-gradient-to-r from-emerald-700 to-emerald-600 hover:from-emerald-800 hover:to-emerald-700 text-amber-300 rounded-lg text-sm font-bold border border-amber-400/40">Atualizar Cliente</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Script de Busca Automática de CEP e Preenchimento dos Campos de Endereço -->
    <script>
        function aplicarMascaraCep(input) {
            let valor = input.value.replace(/\D/g, '');
            if (valor.length > 8) valor = valor.substring(0, 8);
            if (valor.length > 5) {
                input.value = valor.substring(0, 5) + '-' + valor.substring(5);
            } else {
                input.value = valor;
            }
            if (valor.length === 8) {
                buscarCep(valor);
            }
        }

        async function buscarCep(cep) {
            const limpo = (cep || document.getElementById('cep').value || '').replace(/\D/g, '');
            if (limpo.length !== 8) return;

            const feedback = document.getElementById('cep-feedback');
            const spinner = document.getElementById('cep-spinner');
            
            if (feedback) {
                feedback.textContent = 'Buscando endereço pelo CEP...';
                feedback.className = 'text-xs text-amber-500 dark:text-amber-400 mt-1';
            }
            if (spinner) spinner.classList.remove('hidden');

            try {
                let dados = null;

                // 1. Tenta ViaCEP diretamente
                try {
                    const res = await fetch(`https://viacep.com.br/ws/${limpo}/json/`);
                    if (res.ok) {
                        const json = await res.json();
                        if (!json.erro) {
                            dados = json;
                        }
                    }
                } catch (e) {
                    dados = null;
                }

                // 2. Se falhar, tenta nossa rota proxy local no backend
                if (!dados) {
                    try {
                        const resLocal = await fetch(`/consulta-cep/${limpo}`);
                        if (resLocal.ok) {
                            const jsonLocal = await resLocal.json();
                            if (!jsonLocal.erro) {
                                dados = jsonLocal;
                            }
                        }
                    } catch (e) {
                        dados = null;
                    }
                }

                if (dados && !dados.erro) {
                    if (dados.logradouro) document.getElementById('endereco').value = dados.logradouro;
                    if (dados.bairro) document.getElementById('bairro').value = dados.bairro;
                    if (dados.localidade) document.getElementById('cidade').value = dados.localidade;
                    if (dados.uf) document.getElementById('estado').value = dados.uf.toUpperCase();
                    if (dados.complemento && !document.getElementById('complemento').value) {
                        document.getElementById('complemento').value = dados.complemento;
                    }

                    if (feedback) {
                        feedback.textContent = '✓ Endereço preenchido automaticamente!';
                        feedback.className = 'text-xs text-emerald-600 dark:text-emerald-400 font-semibold mt-1';
                        setTimeout(() => { if (feedback) feedback.textContent = ''; }, 4000);
                    }

                    // Move o foco para o campo Número se estiver vazio
                    const campoNumero = document.getElementById('numero');
                    if (campoNumero && !campoNumero.value) {
                        campoNumero.focus();
                    }
                } else {
                    if (feedback) {
                        feedback.textContent = 'CEP não encontrado. Digite o endereço manualmente.';
                        feedback.className = 'text-xs text-amber-600 dark:text-amber-400 mt-1';
                    }
                }
            } catch (err) {
                if (feedback) {
                    feedback.textContent = 'Não foi possível consultar o CEP. Preencha manualmente.';
                    feedback.className = 'text-xs text-amber-600 dark:text-amber-400 mt-1';
                }
            } finally {
                if (spinner) spinner.classList.add('hidden');
            }
        }
    </script>
</x-app-layout>
