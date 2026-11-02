<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Contrato de Locação Nº {{ $dados['numero_contrato'] }} - {{ $cliente->nome }}</title>
    <style>
        @page {
            margin: 22px 28px 22px 28px;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            color: #1e293b;
            font-size: 11px;
            line-height: 1.45;
            margin: 0;
            padding: 0;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #059669;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .logo-img {
            height: 52px;
            width: auto;
        }
        .title {
            font-size: 17px;
            font-weight: bold;
            color: #064e3b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .contract-meta {
            text-align: right;
            font-size: 11px;
            color: #475569;
        }
        .contract-number {
            font-size: 14px;
            font-weight: bold;
            color: #d97706;
        }
        .box {
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            padding: 8px 10px;
            margin-bottom: 10px;
        }
        .box-title {
            font-size: 11px;
            font-weight: bold;
            color: #064e3b;
            text-transform: uppercase;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 3px;
            margin-bottom: 5px;
            letter-spacing: 0.5px;
        }
        .grid-table {
            width: 100%;
            border-collapse: collapse;
        }
        .grid-table td {
            padding: 2px 4px;
            font-size: 10.5px;
            vertical-align: top;
        }
        .label {
            font-weight: bold;
            color: #334155;
        }
        .table-data {
            width: 100%;
            border-collapse: collapse;
            margin: 8px 0;
        }
        .table-data th {
            background-color: #064e3b;
            color: #ffffff;
            font-size: 10px;
            text-transform: uppercase;
            padding: 5px 8px;
            text-align: left;
            border: 1px solid #064e3b;
        }
        .table-data td {
            border: 1px solid #cbd5e1;
            padding: 6px 8px;
            font-size: 10.5px;
        }
        .clause-title {
            font-weight: bold;
            color: #0f172a;
            margin-top: 8px;
            margin-bottom: 2px;
            font-size: 10.5px;
        }
        .clause-text {
            text-align: justify;
            margin-bottom: 6px;
            font-size: 10.5px;
            color: #334155;
        }
        .highlight-badge {
            display: inline-block;
            background-color: #fef3c7;
            color: #92400e;
            padding: 2px 6px;
            font-weight: bold;
            border-radius: 3px;
            border: 1px solid #fde68a;
        }
        .signatures {
            margin-top: 25px;
            width: 100%;
            page-break-inside: avoid;
        }
        .signatures table {
            width: 100%;
        }
        .signature-line {
            border-top: 1px solid #475569;
            width: 90%;
            margin: 0 auto;
            padding-top: 4px;
            text-align: center;
            font-size: 10px;
            font-weight: bold;
            color: #1e293b;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>

    <!-- Cabeçalho -->
    <table class="header-table">
        <tr>
            <td style="width: 50%;">
                <div class="title">Contrato de Locação de Tendas</div>
                <div style="font-size: 10px; color: #059669; font-weight: bold;">TENDAS ELDORADO — VENDA E LOCAÇÃO</div>
            </td>
            <td class="contract-meta">
                <div class="contract-number">Contrato Nº {{ $dados['numero_contrato'] }}</div>
                <div>{{ $dados['data_extenso'] }}</div>
            </td>
        </tr>
    </table>

    <!-- CONTRATADA -->
    <div class="box">
        <div class="box-title">Contratada (Locadora)</div>
        <table class="grid-table">
            <tr>
                <td colspan="2"><span class="label">Razão Social:</span> 58.950.200 MARCIA REGINA SABINO GIBERTONI</td>
                <td><span class="label">Fantasia:</span> TENDAS ELDORADO</td>
            </tr>
            <tr>
                <td><span class="label">CNPJ:</span> 58.950.200/0001-95</td>
                <td><span class="label">IE:</span> ISENTO</td>
                <td><span class="label">Telefone:</span> (17) 99107-8924 / (17) 99155-5335</td>
            </tr>
            <tr>
                <td colspan="3">
                    <span class="label">Endereço:</span> Rua Ruth Tranjan Donegá, 11 - Parque Residencial Buona Vita - CEP: 15077-429 - São José do Rio Preto / SP
                </td>
            </tr>
        </table>
    </div>

    <!-- CONTRATANTE -->
    <div class="box">
        <div class="box-title">Contratante (Locatário)</div>
        <table class="grid-table">
            <tr>
                <td colspan="2"><span class="label">Nome / Razão Social:</span> {{ $cliente->nome }}</td>
                <td><span class="label">Telefone / WhatsApp:</span> {{ $cliente->telefone }}</td>
            </tr>
            <tr>
                <td><span class="label">CPF / CNPJ:</span> {{ $cliente->cpf_cnpj ?: 'Não informado' }}</td>
                <td><span class="label">RG / IE:</span> {{ $cliente->rg ?? 'Isento/Não informado' }}</td>
                <td><span class="label">E-mail:</span> {{ $cliente->email ?: 'Não informado' }}</td>
            </tr>
            <tr>
                <td colspan="3">
                    <span class="label">Endereço Principal:</span> {{ $cliente->endereco }} 
                    @if($cliente->cidade) - Cidade: {{ $cliente->cidade }} @endif
                    @if($cliente->cep) - CEP: {{ $cliente->cep }} @endif
                </td>
            </tr>
        </table>
    </div>

    <p style="margin: 6px 0; font-size: 10.5px; color: #334155; font-style: italic;">
        Tem justo e contratado o seguinte, que mutuamente outorgam e aceitam, a saber:
        O primeiro nomeado aqui chamado <strong>locador(a)</strong>, sendo possuidor e proprietário, se compromete em alugar:
    </p>

    <!-- PRODUTO(S) / TENDAS -->
    <table class="table-data">
        <thead>
            <tr>
                <th style="width: 55%;">Item - Produto(s) / Dimensões</th>
                <th style="width: 20%; text-align: center;">Quantidade</th>
                <th style="width: 25%; text-align: right;">Total da Locação</th>
            </tr>
        </thead>
        <tbody>
            @forelse($dados['itens'] as $item)
                <tr>
                    <td><strong>{{ $item['nome'] }}</strong> - {{ $item['tamanho'] }}</td>
                    <td style="text-align: center;">{{ $item['quantidade'] }} {{ $item['quantidade'] > 1 ? 'PEÇAS' : 'PEÇA' }}</td>
                    <td style="text-align: right; font-weight: bold; color: #064e3b;">R$ {{ $item['total'] }}</td>
                </tr>
            @empty
                <tr>
                    <td><strong>TENDA PIRAMIDAL</strong> - Conforme especificação do pedido</td>
                    <td style="text-align: center;">1 PEÇA</td>
                    <td style="text-align: right; font-weight: bold; color: #064e3b;">R$ {{ $dados['valor_total'] }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <p style="margin: 6px 0; font-size: 10.5px; color: #334155;">
        Ao segundo nomeado aqui designado o <strong>locatário</strong>, concorda e aceita com o presente instrumento, mediante as cláusulas e condições adiante estipuladas, ou seja:
    </p>

    <!-- CLÁUSULAS CONTRATUAIS -->
    <div class="clause-title">1ª) O prazo de locação é de:</div>
    <table class="table-data" style="margin-top: 3px; margin-bottom: 4px;">
        <thead>
            <tr style="background-color: #0f766e;">
                <th style="width: 25%; text-align: center;">Saída / Instalação</th>
                <th style="width: 25%; text-align: center;">Início do Evento</th>
                <th style="width: 25%; text-align: center;">Fim do Evento</th>
                <th style="width: 25%; text-align: center;">Retorno / Retirada</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="text-align: center; font-weight: bold;">{{ $dados['saida'] }}</td>
                <td style="text-align: center; font-weight: bold;">{{ $dados['inicio'] }}</td>
                <td style="text-align: center; font-weight: bold;">{{ $dados['fim'] }}</td>
                <td style="text-align: center; font-weight: bold;">{{ $dados['retorno'] }}</td>
            </tr>
        </tbody>
    </table>
    <div class="clause-text">
        Data em que o locatário se obriga a restituir <strong>A TENDA</strong>, no estado em que recebeu, sem avarias, independentemente de notificação ou interpelação judicial.<br>
        <strong>PARÁGRAFO PRIMEIRO:</strong> O locatário pagará o aluguel enquanto estiver na posse da TENDA, o aluguel mensal/diário combinado, até a efetiva devolução do objeto deste instrumento, o locatário concorda e é ciente.
    </div>

    <div class="clause-title">2ª) O aluguel será no valor de:</div>
    <div class="clause-text">
        <strong>Pagamento(s):</strong> À VISTA com vencimento para <strong>{{ $dados['vencimento'] }}</strong> no valor total de <span class="highlight-badge">R$ {{ $dados['valor_total'] }}</span>. Deverá ser pago sempre à locadora na forma e data de vencimento descritas.
    </div>

    <div class="clause-title">4ª) Garantia do bem Locado:</div>
    <div class="clause-text">
        Como garantia, a <strong>LOCATÁRIA</strong> assinará o contrato a favor do <strong>LOCADOR</strong> no valor do bem Locado de <strong>R$ 20.500,00 (vinte mil e quinhentos reais)</strong>, no caso de não devolução das tendas / fechamentos.
    </div>

    <div class="clause-title">5ª) Cessão e Sublocação:</div>
    <div class="clause-text">
        À Locatária não é permitido, sob qualquer que seja o pretexto, a sublocação, transferência de local de instalação, sub-rogação, empréstimo, comodato, ou cessão, no todo ou em parte, sem preceder consentimento por escrito da locadora, o qual poderá recusar-lhe consenso, sem alegar razões, a fim de que a TENDA esteja desimpedida no término do presente contrato.
    </div>

    <div class="clause-title">6ª) Condições de Entrega e Devolução:</div>
    <div class="clause-text">
        A locatária declara que havendo recebido a TENDA em ótimas condições, tem que ser devolvido nas mesmas condições ao final da locação.
    </div>

    <div class="clause-title">7ª) Direito de Vistoria:</div>
    <div class="clause-text">
        À locadora fica reservado o direito de, quando lhe parecer conveniente, visitar o objeto para vistoriá-lo, quanto ao seu estado.
    </div>

    <div class="clause-title">8ª) Serviços Sanitários e Órgãos Públicos:</div>
    <div class="clause-text">
        Nenhuma intimação do serviço sanitário ou fiscalização será motivo para o locatário abandonar o objeto ou pedir a rescisão deste contrato.
    </div>

    <div class="clause-title">9ª) Foro Competente:</div>
    <div class="clause-text">
        Para todas as questões resultantes deste contrato, será competente o foro da situação da comarca de São José do Rio Preto / SP, seja qual for o domicílio dos contratantes.
    </div>

    <div class="clause-title">10ª) Inadimplência e Rescisão:</div>
    <div class="clause-text">
        A violação de qualquer cláusula contratual, inclusive no caso de inadimplência do locatário, será facultado à locadora considerar o presente contrato rescindido de pleno direito, independentemente de interpelação, aviso ou notificação judicial ou extrajudicial, obrigando-se o locatário à imediata restituição e devolução da TENDA, nas condições ajustadas neste instrumento, sujeitando-se ao valor correspondente ao bem locado sem prejuízo dos aluguéis e encargos que por ventura estejam em atraso e demais sanções contratuais, obrigando-se, ainda o locatário, ao pagamento de honorários advocatícios de 20% (vinte por cento) e despesas judiciais, no caso de haver necessidade de qualquer ação.
    </div>

    <div class="clause-title">11ª) Retirada da Estrutura:</div>
    <div class="clause-text">
        Findo ou rescindido o presente contrato, o locatário dará à locadora o direito da retirada da Tenda e fechamentos instalados, sem aviso prévio. Permanecerá o presente contrato com todas suas cláusulas, condições e obrigações em vigor, enquanto não se verifique a entrega e devolução da mesma, após vistoria e conferência das condições da Tenda, e finalmente a rescisão deste instrumento.
    </div>

    <div class="clause-title">12ª) Cobrança Judicial:</div>
    <div class="clause-text">
        Tudo quanto for devido em razão deste contrato e que não comporte o processo executivo, será cobrado em ação competente, ficando a cargo do devedor, em qualquer caso, os honorários do advogado que o credor constituir para ressalva dos seus direitos.
    </div>

    <div class="clause-title">15ª) Falecimento:</div>
    <div class="clause-text">
        Em caso de falecimento de qualquer parte contratante, os herdeiros da parte falecida serão obrigados ao cumprimento integral deste contrato, até a sua terminação.
    </div>

    <div class="clause-title">16ª) Reajuste e Prorrogação:</div>
    <div class="clause-text">
        O valor do aluguel poderá ser reajustado na hipótese de ocorrer a prorrogação desta locação, de acordo com negociação entre as partes.
    </div>

    @if(!empty($dados['observacoes']))
        <div class="box" style="margin-top: 8px;">
            <div class="box-title">Observações do Pedido / Local da Montagem</div>
            <div style="font-size: 10px; color: #334155;">{{ $dados['observacoes'] }}</div>
        </div>
    @endif

    <p style="margin: 12px 0 20px 0; font-size: 10px; text-align: justify; color: #475569;">
        E por estarem justos e contratados, depois de lido e achado conforme, aceitam e assinam o presente contrato, em 02 (duas) vias de igual teor e forma, para um só efeito, na presença das testemunhas abaixo.
    </p>

    <!-- ASSINATURAS -->
    <div class="signatures">
        <table style="width: 100%;">
            <tr>
                <td style="width: 48%; text-align: center; vertical-align: top; padding: 15px 5px;">
                    <div class="signature-line">
                        {{ $cliente->nome }}<br>
                        <span style="font-size: 9px; font-weight: normal; color: #64748b;">CONTRATANTE</span>
                    </div>
                </td>
                <td style="width: 48%; text-align: center; vertical-align: top; padding: 15px 5px;">
                    <div class="signature-line">
                        58.950.200 MARCIA REGINA SABINO GIBERTONI<br>
                        <span style="font-size: 9px; font-weight: normal; color: #64748b;">CONTRATADA (TENDAS ELDORADO)</span>
                    </div>
                </td>
            </tr>
            <tr>
                <td style="width: 48%; text-align: center; vertical-align: top; padding: 25px 5px 0 5px;">
                    <div class="signature-line">
                        1ª Testemunha<br>
                        <span style="font-size: 9px; font-weight: normal; color: #64748b;">Nome / CPF:</span>
                    </div>
                </td>
                <td style="width: 48%; text-align: center; vertical-align: top; padding: 25px 5px 0 5px;">
                    <div class="signature-line">
                        2ª Testemunha<br>
                        <span style="font-size: 9px; font-weight: normal; color: #64748b;">Nome / CPF:</span>
                    </div>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>

