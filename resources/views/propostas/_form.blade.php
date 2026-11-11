@php
    $p = $proposta ?? null;
    $padrao = $dadosPadrao ?? \App\Models\Proposta::dadosPadraoLocadora(Auth::user());

    $val = fn (string $campo, mixed $default = '') => old($campo, $p?->{$campo} ?? ($padrao[$campo] ?? $default));

    $itensIniciais = old('itens');
    if ($itensIniciais === null) {
        if ($p && $p->relationLoaded('itens') && $p->itens->isNotEmpty()) {
            $itensIniciais = $p->itens->map(fn ($item) => [
                'tenda_id' => $item->tenda_id ? (string) $item->tenda_id : '',
                'descricao' => $item->descricao,
                'quantidade' => (int) $item->quantidade,
                'valor_periodo' => number_format((float) $item->valor_periodo, 2, ',', '.'),
            ])->values()->all();
        } else {
            $itensIniciais = [];
        }
    } else {
        $itensIniciais = collect($itensIniciais)->map(fn ($item) => [
            'tenda_id' => (string) ($item['tenda_id'] ?? ''),
            'descricao' => (string) ($item['descricao'] ?? ''),
            'quantidade' => max(1, (int) ($item['quantidade'] ?? 1)),
            'valor_periodo' => (string) ($item['valor_periodo'] ?? '0,00'),
        ])->values()->all();
    }

    $clientesJson = $clientes->mapWithKeys(fn ($c) => [
        $c->id => [
            'nome' => $c->nome,
            'cpf_cnpj' => $c->cpf_cnpj ?? '',
            'cep' => $c->cep ?? '',
            'endereco' => trim(($c->endereco ?? '') . ($c->numero ? ', ' . $c->numero : '') . ($c->complemento ? ' - ' . $c->complemento : '')),
            'bairro' => $c->bairro ?? '',
            'cidade' => $c->cidade ?? '',
            'uf' => $c->estado ?? '',
            'telefone' => $c->telefone ?? '',
            'email' => $c->email ?? '',
        ],
    ]);

    $tendasJson = $tendas->map(fn ($t) => [
        'id' => (string) $t->id,
        'nome' => $t->nome,
        'tamanho' => $t->tamanho,
        'valor_diaria' => (float) ($t->valor_diaria ?? 0),
    ])->values();

    $valorTotalInicial = old('valor_total', $p ? number_format((float) $p->valor_total, 2, ',', '.') : '0,00');
    $valorDescontoInicial = old('valor_desconto', $p ? number_format((float) $p->valor_desconto, 2, ',', '.') : '0,00');
    $valorTotalGeralInicial = old('valor_total_geral', $p ? number_format((float) $p->valor_total_geral, 2, ',', '.') : '0,00');
@endphp

@if($errors->any())
    <div class="mb-6 p-4 rounded-xl bg-red-50 dark:bg-red-950/50 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-200 text-sm">
        <div class="font-bold mb-1">Verifique os campos antes de salvar:</div>
        <ul class="list-disc list-inside space-y-0.5 text-xs">
            @foreach($errors->all() as $erro)
                <li>{{ $erro }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div x-data="propostaFormManager({
        itens: @js($itensIniciais),
        tendas: @js($tendasJson),
        clientes: @js($clientesJson),
        valorTotal: @js($valorTotalInicial),
        valorDesconto: @js($valorDescontoInicial),
        valorTotalGeral: @js($valorTotalGeralInicial),
     })"
     class="space-y-8">

    <!-- SEÇÃO 1: IDENTIFICAÇÃO DA PROPOSTA -->
    <div class="space-y-4">
        <div class="flex items-center gap-2">
            <span class="w-7 h-7 rounded-full bg-emerald-600 text-white font-bold flex items-center justify-center text-sm shadow-xs">1</span>
            <h3 class="font-bold text-gray-900 dark:text-white text-base">Proposta de Locação & Identificação</h3>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-emerald-200 mb-1">Nº da Proposta *</label>
                <input type="text"
                       name="numero"
                       value="{{ $val('numero', $proximoNumero ?? '1') }}"
                       required
                       placeholder="Ex: 88"
                       class="w-full bg-white dark:bg-[#02140e] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm font-bold">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-emerald-200 mb-1">Data da Proposta *</label>
                <input type="date"
                       name="data_proposta"
                       value="{{ old('data_proposta', $p?->data_proposta?->format('Y-m-d') ?? date('Y-m-d')) }}"
                       required
                       class="w-full bg-white dark:bg-[#02140e] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm font-medium">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-emerald-200 mb-1">Identificação (Evento / Projeto)</label>
                <input type="text"
                       name="identificacao"
                       value="{{ $val('identificacao') }}"
                       placeholder="Ex: MULHERES EMPREENDEDORA NATAL"
                       class="w-full bg-white dark:bg-[#02140e] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-emerald-200 mb-1">Status da Proposta</label>
                <select name="status" class="w-full bg-white dark:bg-[#02140e] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm font-semibold">
                    <option value="rascunho" {{ $val('status', 'rascunho') === 'rascunho' ? 'selected' : '' }}>Rascunho</option>
                    <option value="enviada" {{ $val('status') === 'enviada' ? 'selected' : '' }}>Enviada</option>
                    <option value="aprovada" {{ $val('status') === 'aprovada' ? 'selected' : '' }}>Aprovada</option>
                    <option value="recusada" {{ $val('status') === 'recusada' ? 'selected' : '' }}>Recusada</option>
                </select>
            </div>
        </div>
    </div>

    <!-- SEÇÃO 2: DADOS DE (LOCADORA / CONSULTOR) -->
    <div class="border-t border-gray-200 dark:border-[#083d2c] pt-6 space-y-4">
        <div class="flex items-center justify-between flex-wrap gap-2">
            <div class="flex items-center gap-2">
                <span class="w-7 h-7 rounded-full bg-emerald-600 text-white font-bold flex items-center justify-center text-sm shadow-xs">2</span>
                <h3 class="font-bold text-gray-900 dark:text-white text-base">De: (Dados da Locadora / Consultor)</h3>
            </div>
            <span class="text-xs text-emerald-600 dark:text-emerald-400 font-medium">Pré-preenchido com os dados da Tendas Eldorado (editável)</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4">
            <div class="lg:col-span-3">
                <label class="block text-xs font-semibold text-gray-700 dark:text-emerald-200 mb-1">Razão Social *</label>
                <input type="text" name="de_razao_social" value="{{ $val('de_razao_social') }}" required class="w-full bg-white dark:bg-[#02140e] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm">
            </div>

            <div class="lg:col-span-3">
                <label class="block text-xs font-semibold text-gray-700 dark:text-emerald-200 mb-1">Fantasia</label>
                <input type="text" name="de_fantasia" value="{{ $val('de_fantasia') }}" class="w-full bg-white dark:bg-[#02140e] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm">
            </div>

            <div class="lg:col-span-2">
                <label class="block text-xs font-semibold text-gray-700 dark:text-emerald-200 mb-1">CNPJ</label>
                <input type="text" name="de_cnpj" value="{{ $val('de_cnpj') }}" class="w-full bg-white dark:bg-[#02140e] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm">
            </div>

            <div class="lg:col-span-2">
                <label class="block text-xs font-semibold text-gray-700 dark:text-emerald-200 mb-1">IE (Inscrição Estadual)</label>
                <input type="text" name="de_ie" value="{{ $val('de_ie') }}" class="w-full bg-white dark:bg-[#02140e] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm">
            </div>

            <div class="lg:col-span-2">
                <label class="block text-xs font-semibold text-gray-700 dark:text-emerald-200 mb-1">IM (Inscrição Municipal)</label>
                <input type="text" name="de_im" value="{{ $val('de_im') }}" placeholder="Opcional" class="w-full bg-white dark:bg-[#02140e] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm">
            </div>

            <div class="lg:col-span-1">
                <label class="block text-xs font-semibold text-gray-700 dark:text-emerald-200 mb-1">CEP</label>
                <input type="text" id="de_cep" name="de_cep" value="{{ $val('de_cep') }}" @input="onCepInput($event.target, 'de')" placeholder="00000-000" maxlength="9" class="w-full bg-white dark:bg-[#02140e] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm">
            </div>

            <div class="lg:col-span-2">
                <label class="block text-xs font-semibold text-gray-700 dark:text-emerald-200 mb-1">Endereço</label>
                <input type="text" id="de_endereco" name="de_endereco" value="{{ $val('de_endereco') }}" class="w-full bg-white dark:bg-[#02140e] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm">
            </div>

            <div class="lg:col-span-1">
                <label class="block text-xs font-semibold text-gray-700 dark:text-emerald-200 mb-1">Bairro</label>
                <input type="text" id="de_bairro" name="de_bairro" value="{{ $val('de_bairro') }}" class="w-full bg-white dark:bg-[#02140e] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm">
            </div>

            <div class="lg:col-span-1">
                <label class="block text-xs font-semibold text-gray-700 dark:text-emerald-200 mb-1">Cidade</label>
                <input type="text" id="de_cidade" name="de_cidade" value="{{ $val('de_cidade') }}" class="w-full bg-white dark:bg-[#02140e] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm">
            </div>

            <div class="lg:col-span-1">
                <label class="block text-xs font-semibold text-gray-700 dark:text-emerald-200 mb-1">UF</label>
                <input type="text" id="de_uf" name="de_uf" value="{{ $val('de_uf') }}" maxlength="2" class="w-full uppercase bg-white dark:bg-[#02140e] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm">
            </div>

            <div class="lg:col-span-2">
                <label class="block text-xs font-semibold text-gray-700 dark:text-emerald-200 mb-1">Consultor</label>
                <input type="text" name="de_consultor" value="{{ $val('de_consultor') }}" placeholder="Nome do consultor" class="w-full bg-white dark:bg-[#02140e] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm">
            </div>

            <div class="lg:col-span-2">
                <label class="block text-xs font-semibold text-gray-700 dark:text-emerald-200 mb-1">Celular</label>
                <input type="text" name="de_celular" value="{{ $val('de_celular') }}" placeholder="(17) 99142-9132" class="w-full bg-white dark:bg-[#02140e] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm">
            </div>

            <div class="lg:col-span-2">
                <label class="block text-xs font-semibold text-gray-700 dark:text-emerald-200 mb-1">Email</label>
                <input type="email" name="de_email" value="{{ $val('de_email') }}" placeholder="contato@tendaseldorado.com.br" class="w-full bg-white dark:bg-[#02140e] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm">
            </div>
        </div>
    </div>

    <!-- SEÇÃO 3: DADOS PARA (DESTINATÁRIO / CLIENTE) -->
    <div class="border-t border-gray-200 dark:border-[#083d2c] pt-6 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-2">
                <span class="w-7 h-7 rounded-full bg-emerald-600 text-white font-bold flex items-center justify-center text-sm shadow-xs">3</span>
                <h3 class="font-bold text-gray-900 dark:text-white text-base">Para: (Dados do Cliente / Destinatário)</h3>
            </div>

            <div class="w-full sm:w-80">
                <select name="cliente_id"
                        x-on:change="importarCliente($event.target.value)"
                        class="w-full bg-emerald-50/70 dark:bg-[#021811] border-emerald-300 dark:border-emerald-700/60 text-gray-900 dark:text-emerald-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-xs font-semibold">
                    <option value="">Importar de Cliente Cadastrado (Opcional)...</option>
                    @foreach($clientes as $c)
                        <option value="{{ $c->id }}" {{ old('cliente_id', $p?->cliente_id) == $c->id ? 'selected' : '' }}>
                            {{ $c->nome }} {{ $c->cpf_cnpj ? "({$c->cpf_cnpj})" : '' }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4">
            <div class="lg:col-span-3">
                <label class="block text-xs font-semibold text-gray-700 dark:text-emerald-200 mb-1">Razão Social / Nome *</label>
                <input type="text" id="para_razao_social" name="para_razao_social" value="{{ $val('para_razao_social') }}" required placeholder="Ex: MUNICIPIO DE MIRASSOL" class="w-full bg-white dark:bg-[#02140e] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm font-semibold">
            </div>

            <div class="lg:col-span-3">
                <label class="block text-xs font-semibold text-gray-700 dark:text-emerald-200 mb-1">Fantasia</label>
                <input type="text" id="para_fantasia" name="para_fantasia" value="{{ $val('para_fantasia') }}" placeholder="Ex: Mirassol Prefeitura Municipal" class="w-full bg-white dark:bg-[#02140e] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm">
            </div>

            <div class="lg:col-span-3">
                <label class="block text-xs font-semibold text-gray-700 dark:text-emerald-200 mb-1">CNPJ / CPF</label>
                <input type="text" id="para_cnpj_cpf" name="para_cnpj_cpf" value="{{ $val('para_cnpj_cpf') }}" placeholder="Ex: 46.612.032/0001-49" class="w-full bg-white dark:bg-[#02140e] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm">
            </div>

            <div class="lg:col-span-3">
                <label class="block text-xs font-semibold text-gray-700 dark:text-emerald-200 mb-1">Inscrição Estadual</label>
                <input type="text" id="para_ie" name="para_ie" value="{{ $val('para_ie') }}" placeholder="Ex: ISENTO ou número da IE" class="w-full bg-white dark:bg-[#02140e] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm">
            </div>

            <div class="lg:col-span-1">
                <label class="block text-xs font-semibold text-gray-700 dark:text-emerald-200 mb-1">
                    CEP <span class="text-[10px] text-amber-500">(auto)</span>
                </label>
                <input type="text" id="para_cep" name="para_cep" value="{{ $val('para_cep') }}" @input="onCepInput($event.target, 'para')" placeholder="00000-000" maxlength="9" class="w-full bg-white dark:bg-[#02140e] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm">
                <p id="para_cep_feedback" class="text-[10px] mt-0.5 text-emerald-500"></p>
            </div>

            <div class="lg:col-span-2">
                <label class="block text-xs font-semibold text-gray-700 dark:text-emerald-200 mb-1">Endereço</label>
                <input type="text" id="para_endereco" name="para_endereco" value="{{ $val('para_endereco') }}" placeholder="Rua / Praça, número" class="w-full bg-white dark:bg-[#02140e] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm">
            </div>

            <div class="lg:col-span-1">
                <label class="block text-xs font-semibold text-gray-700 dark:text-emerald-200 mb-1">Bairro</label>
                <input type="text" id="para_bairro" name="para_bairro" value="{{ $val('para_bairro') }}" placeholder="Ex: CENTRO" class="w-full bg-white dark:bg-[#02140e] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm">
            </div>

            <div class="lg:col-span-1">
                <label class="block text-xs font-semibold text-gray-700 dark:text-emerald-200 mb-1">Cidade</label>
                <input type="text" id="para_cidade" name="para_cidade" value="{{ $val('para_cidade') }}" placeholder="Ex: MIRASSOL" class="w-full bg-white dark:bg-[#02140e] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm">
            </div>

            <div class="lg:col-span-1">
                <label class="block text-xs font-semibold text-gray-700 dark:text-emerald-200 mb-1">UF</label>
                <input type="text" id="para_uf" name="para_uf" value="{{ $val('para_uf') }}" maxlength="2" placeholder="SP" class="w-full uppercase bg-white dark:bg-[#02140e] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm">
            </div>

            <div class="lg:col-span-2">
                <label class="block text-xs font-semibold text-gray-700 dark:text-emerald-200 mb-1">Solicitante</label>
                <input type="text" id="para_solicitante" name="para_solicitante" value="{{ $val('para_solicitante') }}" placeholder="Ex: SAMARA" class="w-full bg-white dark:bg-[#02140e] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm">
            </div>

            <div class="lg:col-span-2">
                <label class="block text-xs font-semibold text-gray-700 dark:text-emerald-200 mb-1">Celular</label>
                <input type="text" id="para_celular" name="para_celular" value="{{ $val('para_celular') }}" placeholder="Ex: 17991447381" class="w-full bg-white dark:bg-[#02140e] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm">
            </div>

            <div class="lg:col-span-2">
                <label class="block text-xs font-semibold text-gray-700 dark:text-emerald-200 mb-1">Email</label>
                <input type="email" id="para_email" name="para_email" value="{{ $val('para_email') }}" placeholder="email@cliente.com.br" class="w-full bg-white dark:bg-[#02140e] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm">
            </div>
        </div>
    </div>

    <!-- SEÇÃO 4: DATA DO EVENTO -->
    <div class="border-t border-gray-200 dark:border-[#083d2c] pt-6 space-y-4">
        <div class="flex items-center gap-2">
            <span class="w-7 h-7 rounded-full bg-emerald-600 text-white font-bold flex items-center justify-center text-sm shadow-xs">4</span>
            <h3 class="font-bold text-gray-900 dark:text-white text-base">Data do Evento</h3>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 max-w-xl">
            <!-- De: -->
            <div class="p-3.5 rounded-xl bg-gray-50 dark:bg-[#021811] border border-gray-200 dark:border-[#083d2c] space-y-1.5">
                <label class="block text-xs font-bold uppercase tracking-wider text-emerald-700 dark:text-amber-400">De (Data Inicial)</label>
                <input type="date"
                       name="data_inicio"
                       id="data_inicio"
                       value="{{ old('data_inicio', $p?->data_inicio?->format('Y-m-d') ?? ($p?->data_saida?->format('Y-m-d') ?? '')) }}"
                       class="w-full bg-white dark:bg-[#031d14] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm">
            </div>

            <!-- Até: -->
            <div class="p-3.5 rounded-xl bg-gray-50 dark:bg-[#021811] border border-gray-200 dark:border-[#083d2c] space-y-1.5">
                <label class="block text-xs font-bold uppercase tracking-wider text-emerald-700 dark:text-amber-400">Até (Data Final)</label>
                <input type="date"
                       name="data_fim"
                       id="data_fim"
                       value="{{ old('data_fim', $p?->data_fim?->format('Y-m-d') ?? ($p?->data_retorno?->format('Y-m-d') ?? '')) }}"
                       class="w-full bg-white dark:bg-[#031d14] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm">
            </div>
        </div>
    </div>

    <!-- SEÇÃO 5: PRODUTO(S) & TOTAIS -->
    <div class="border-t border-gray-200 dark:border-[#083d2c] pt-6 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-2">
                <span class="w-7 h-7 rounded-full bg-emerald-600 text-white font-bold flex items-center justify-center text-sm shadow-xs">5</span>
                <h3 class="font-bold text-gray-900 dark:text-white text-base">Produto(s), Quantidade e Valor/Período</h3>
            </div>

            <button type="button"
                    @click="addItem()"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-amber-400 hover:bg-amber-300 text-slate-950 font-bold text-xs shadow-xs transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Adicionar Produto / Item</span>
            </button>
        </div>

        <div class="space-y-3">
            <template x-if="itens.length === 0">
                <div class="p-6 text-center rounded-xl border border-dashed border-gray-300 dark:border-emerald-800/60 bg-gray-50/70 dark:bg-[#021811] text-sm text-gray-500 dark:text-emerald-300/70">
                    Nenhum produto adicionado ainda. Clique em <strong>Adicionar Produto / Item</strong> acima para incluir tendas ou itens.
                </div>
            </template>

            <template x-for="(item, index) in itens" :key="index">
                <div class="p-4 rounded-xl bg-gray-50 dark:bg-[#021811] border border-gray-200 dark:border-[#083d2c] grid grid-cols-1 lg:grid-cols-12 gap-3 items-end">
                    <div class="lg:col-span-4">
                        <label class="block text-xs font-semibold text-gray-600 dark:text-emerald-300 mb-1">Selecionar Tenda do Estoque (Opcional)</label>
                        <select :name="`itens[${index}][tenda_id]`"
                                x-model="item.tenda_id"
                                @change="onSelectTenda(index)"
                                class="w-full bg-white dark:bg-[#031d14] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-xs">
                            <option value="">Item personalizado / livre...</option>
                            <template x-for="t in tendas" :key="t.id">
                                <option :value="t.id" :selected="String(t.id) === String(item.tenda_id)" x-text="`${t.nome} (${t.tamanho})`"></option>
                            </template>
                        </select>
                    </div>

                    <div class="lg:col-span-4">
                        <label class="block text-xs font-semibold text-gray-600 dark:text-emerald-300 mb-1">Produto(s) (Descrição no PDF) *</label>
                        <input type="text"
                               :name="`itens[${index}][descricao]`"
                               x-model="item.descricao"
                               placeholder="Ex: TENDA - 10 X 10 metros"
                               class="w-full bg-white dark:bg-[#031d14] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm">
                    </div>

                    <div class="lg:col-span-1">
                        <label class="block text-xs font-semibold text-gray-600 dark:text-emerald-300 mb-1">Qtd</label>
                        <input type="number"
                               min="1"
                               :name="`itens[${index}][quantidade]`"
                               x-model.number="item.quantidade"
                               @input="recalcularPorItens()"
                               class="w-full bg-white dark:bg-[#031d14] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm text-center font-bold">
                    </div>

                    <div class="lg:col-span-2">
                        <label class="block text-xs font-semibold text-gray-600 dark:text-emerald-300 mb-1">Valor/Período (R$)</label>
                        <input type="text"
                               :name="`itens[${index}][valor_periodo]`"
                               x-model="item.valor_periodo"
                               @input="item.valor_periodo = formatarMoeda($event.target.value); recalcularPorItens()"
                               placeholder="0,00"
                               class="w-full bg-white dark:bg-[#031d14] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm font-semibold">
                    </div>

                    <div class="lg:col-span-1 flex justify-end">
                        <button type="button"
                                @click="removeItem(index)"
                                class="p-2 rounded-lg text-red-500 hover:bg-red-50 dark:hover:bg-red-950/40 transition"
                                title="Remover item">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </div>
            </template>
        </div>

        <!-- Totais Financeiros -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 max-w-md">
            <div>
                <label class="block text-xs font-bold uppercase text-emerald-700 dark:text-amber-400 mb-1">Total Geral (R$)</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-xs font-bold text-emerald-700 dark:text-amber-400">R$</span>
                    <input type="text"
                           name="valor_total_geral"
                           x-model="valorTotalGeral"
                           @input="valorTotalGeral = formatarMoeda($event.target.value); valorTotal = valorTotalGeral"
                           class="w-full pl-9 bg-emerald-50/70 dark:bg-[#021811] border-emerald-400 dark:border-amber-400/50 text-emerald-900 dark:text-amber-300 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm font-extrabold">
                </div>
                <input type="hidden" name="valor_total" :value="valorTotalGeral">
                <input type="hidden" name="valor_desconto" value="0,00">
            </div>
        </div>
        <p class="text-[11px] text-gray-400 dark:text-emerald-400/60">
            Ao alterar os itens acima, o <strong>Total Geral</strong> é somado automaticamente pela multiplicação (Qtd × Valor/Período), mas você também pode ajustá-lo manualmente.
        </p>
    </div>

    <!-- SEÇÃO 6: PAGAMENTO(S) E OBSERVAÇÕES -->
    <div class="border-t border-gray-200 dark:border-[#083d2c] pt-6 space-y-4">
        <div class="flex items-center gap-2">
            <span class="w-7 h-7 rounded-full bg-emerald-600 text-white font-bold flex items-center justify-center text-sm shadow-xs">6</span>
            <h3 class="font-bold text-gray-900 dark:text-white text-base">Pagamento(s) e Observações</h3>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-emerald-200 mb-1">Pagamento(s) / Condições</label>
                <textarea id="campo_pagamentos"
                          name="pagamentos"
                          rows="3"
                          placeholder="Ex: À vista via PIX / 50% no pedido + 50% na entrega..."
                          class="w-full bg-white dark:bg-[#02140e] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm">{{ $val('pagamentos') }}</textarea>
                <div class="flex flex-wrap gap-1.5 mt-1.5">
                    @foreach(['À vista via PIX', '50% entrada + 50% na instalação', 'Boleto 30 dias', 'Empenho / Órgão Público'] as $sugestao)
                        <button type="button"
                                @click="adicionarPagamento(@js($sugestao))"
                                class="text-[11px] px-2 py-0.5 rounded-md bg-gray-100 dark:bg-[#04291c] text-gray-700 dark:text-emerald-300 hover:bg-amber-100 dark:hover:bg-amber-500/20 border border-gray-200 dark:border-[#083d2c] transition">
                            + {{ $sugestao }}
                        </button>
                    @endforeach
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-emerald-200 mb-1">Observações</label>
                <textarea name="observacoes"
                          rows="3"
                          placeholder="Validade da proposta, inclusão de montagem/desmontagem, laudos, condições gerais..."
                          class="w-full bg-white dark:bg-[#02140e] border-gray-300 dark:border-[#083d2c] text-gray-900 dark:text-slate-100 focus:border-amber-400 focus:ring-amber-400 rounded-lg text-sm">{{ $val('observacoes') }}</textarea>
            </div>
        </div>
    </div>
</div>

<script>
    function propostaFormManager(config) {
        return {
            itens: Array.isArray(config.itens) ? config.itens : [],
            tendas: Array.isArray(config.tendas) ? config.tendas : [],
            clientes: config.clientes || {},
            valorTotal: config.valorTotal || '0,00',
            valorDesconto: config.valorDesconto || '0,00',
            valorTotalGeral: config.valorTotalGeral || '0,00',

            addItem() {
                this.itens.push({
                    tenda_id: '',
                    descricao: '',
                    quantidade: 1,
                    valor_periodo: '0,00',
                });
            },

            removeItem(index) {
                this.itens.splice(index, 1);
                this.recalcularPorItens();
            },

            onSelectTenda(index) {
                const item = this.itens[index];
                if (!item || !item.tenda_id) return;

                const tenda = this.tendas.find(t => String(t.id) === String(item.tenda_id));
                if (tenda) {
                    item.descricao = tenda.nome !== tenda.tamanho
                        ? `${tenda.nome} (${tenda.tamanho})`
                        : `TENDA - ${tenda.tamanho}`;

                    if (tenda.valor_diaria > 0 && this.parseMoeda(item.valor_periodo) === 0) {
                        item.valor_periodo = this.numeroParaBR(tenda.valor_diaria);
                    }
                    this.recalcularPorItens();
                }
            },

            recalcularPorItens() {
                let soma = 0;
                for (const item of this.itens) {
                    const qtd = Math.max(1, parseInt(item.quantidade || 1, 10));
                    const val = this.parseMoeda(item.valor_periodo);
                    soma += qtd * val;
                }
                this.valorTotal = this.numeroParaBR(soma);
                this.valorTotalGeral = this.numeroParaBR(soma);
            },

            atualizarTotalGeral() {
                const total = this.parseMoeda(this.valorTotal);
                this.valorTotalGeral = this.numeroParaBR(total);
            },

            parseMoeda(val) {
                if (typeof val === 'number') return val;
                const s = String(val || '').trim();
                if (!s) return 0;
                const limpo = s.includes(',')
                    ? s.replace(/\./g, '').replace(',', '.')
                    : s;
                const n = parseFloat(limpo.replace(/[^0-9.-]/g, ''));
                return isNaN(n) ? 0 : Math.max(0, n);
            },

            numeroParaBR(num) {
                return (Number(num) || 0).toLocaleString('pt-BR', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2,
                });
            },

            formatarMoeda(valor) {
                const limpo = String(valor || '').replace(/\D/g, '');
                if (!limpo) return '0,00';
                const num = (parseInt(limpo, 10) / 100).toFixed(2);
                const partes = num.split('.');
                partes[0] = partes[0].replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                return partes.join(',');
            },

            importarCliente(clienteId) {
                if (!clienteId || !this.clientes[clienteId]) return;
                const c = this.clientes[clienteId];

                const setVal = (id, v) => {
                    const el = document.getElementById(id);
                    if (el) el.value = v || '';
                };

                setVal('para_razao_social', c.nome);
                setVal('para_fantasia', c.nome);
                setVal('para_cnpj_cpf', c.cpf_cnpj);
                setVal('para_cep', c.cep);
                setVal('para_endereco', c.endereco);
                setVal('para_bairro', c.bairro);
                setVal('para_cidade', c.cidade);
                setVal('para_uf', c.uf);
                setVal('para_celular', c.telefone);
                setVal('para_email', c.email);
            },

            onCepInput(input, prefixo) {
                let valor = input.value.replace(/\D/g, '');
                if (valor.length > 8) valor = valor.substring(0, 8);
                input.value = valor.length > 5
                    ? valor.substring(0, 5) + '-' + valor.substring(5)
                    : valor;

                if (valor.length === 8) {
                    this.buscarCep(valor, prefixo);
                }
            },

            async buscarCep(cepLimpo, prefixo) {
                const feedback = document.getElementById(`${prefixo}_cep_feedback`);
                if (feedback) feedback.textContent = 'Buscando CEP...';

                let dados = null;
                try {
                    const res = await fetch(`https://viacep.com.br/ws/${cepLimpo}/json/`);
                    if (res.ok) {
                        const json = await res.json();
                        if (!json.erro) dados = json;
                    }
                } catch (e) {
                    dados = null;
                }

                if (!dados) {
                    try {
                        const resLocal = await fetch(`/consulta-cep/${cepLimpo}`);
                        if (resLocal.ok) {
                            const jsonLocal = await resLocal.json();
                            if (!jsonLocal.erro) dados = jsonLocal;
                        }
                    } catch (e) {
                        dados = null;
                    }
                }

                if (dados && !dados.erro) {
                    const setIfPresent = (id, v) => {
                        const el = document.getElementById(id);
                        if (el && v) el.value = v;
                    };
                    setIfPresent(`${prefixo}_endereco`, dados.logradouro);
                    setIfPresent(`${prefixo}_bairro`, dados.bairro);
                    setIfPresent(`${prefixo}_cidade`, dados.localidade);
                    setIfPresent(`${prefixo}_uf`, dados.uf ? dados.uf.toUpperCase() : '');

                    if (feedback) {
                        feedback.textContent = '✓ CEP encontrado';
                        setTimeout(() => { if (feedback) feedback.textContent = ''; }, 3500);
                    }
                } else if (feedback) {
                    feedback.textContent = 'CEP não encontrado';
                    setTimeout(() => { if (feedback) feedback.textContent = ''; }, 3500);
                }
            },

            adicionarPagamento(texto) {
                const el = document.getElementById('campo_pagamentos');
                if (!el) return;
                const atual = el.value.trim();
                el.value = atual ? `${atual}\n${texto}` : texto;
            },
        };
    }
</script>
