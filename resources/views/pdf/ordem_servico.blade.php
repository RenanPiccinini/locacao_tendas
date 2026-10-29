<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Ordem de Serviço - {{ $os->numero_os }}</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            color: #333;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
        }
        .header {
            border-bottom: 2px solid #2563eb;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header table {
            width: 100%;
        }
        .title {
            font-size: 20px;
            font-weight: bold;
            color: #1e3a8a;
        }
        .os-number {
            font-size: 16px;
            font-weight: bold;
            color: #2563eb;
            text-align: right;
        }
        .badge {
            display: inline-block;
            padding: 4px 8px;
            background-color: #dbeafe;
            color: #1e40af;
            font-weight: bold;
            border-radius: 4px;
            text-transform: uppercase;
        }
        .section-title {
            background-color: #f1f5f9;
            padding: 6px 10px;
            font-weight: bold;
            font-size: 13px;
            color: #0f172a;
            margin-top: 15px;
            margin-bottom: 8px;
            border-left: 3px solid #2563eb;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        table.data-table td, table.data-table th {
            padding: 6px 8px;
            border: 1px solid #cbd5e1;
        }
        table.data-table th {
            background-color: #f8fafc;
            text-align: left;
            font-size: 11px;
        }
        .signature-box {
            margin-top: 50px;
            width: 100%;
        }
        .signature-line {
            width: 45%;
            border-top: 1px solid #333;
            text-align: center;
            padding-top: 5px;
            font-size: 11px;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #64748b;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
        }
    </style>
</head>
<body>

    <div class="header">
        <table>
            <tr>
                <td>
                    <div class="title">LOCAÇÃO DE TENDAS</div>
                    <div style="color: #64748b; font-size: 11px;">Sistema de Gestão & Montagem Operacional</div>
                </td>
                <td class="os-number">
                    ORDEM DE SERVIÇO<br>
                    <span>{{ $os->numero_os }}</span><br>
                    <span class="badge">{{ $os->tipo === 'instalacao' ? 'MONTAGEM / INSTALAÇÃO' : 'DESMONTAGEM / RETIRADA' }}</span>
                </td>
            </tr>
        </table>
    </div>

    <!-- Dados do Cliente e Local -->
    <div class="section-title">1. DADOS DO CLIENTE E LOCAL DE MONTAGEM</div>
    <table class="data-table">
        <tr>
            <td style="width: 20%;"><strong>Cliente:</strong></td>
            <td style="width: 40%;">{{ $pedido->cliente->nome }}</td>
            <td style="width: 15%;"><strong>Telefone:</strong></td>
            <td style="width: 25%;">{{ $pedido->cliente->telefone }}</td>
        </tr>
        <tr>
            <td><strong>Endereço:</strong></td>
            <td colspan="3">{{ $pedido->endereco_instalacao }} {{ $pedido->cidade_instalacao ? ' - ' . $pedido->cidade_instalacao : '' }}</td>
        </tr>
        @if($pedido->observacoes)
        <tr>
            <td><strong>Observações:</strong></td>
            <td colspan="3">{{ $pedido->observacoes }}</td>
        </tr>
        @endif
    </table>

    <!-- Programação e Equipe -->
    <div class="section-title">2. PROGRAMAÇÃO E RESPONSÁVEL</div>
    <table class="data-table">
        <tr>
            <td style="width: 25%;"><strong>Data Montagem:</strong></td>
            <td style="width: 25%;">{{ $pedido->data_instalacao->format('d/m/Y') }} {{ $pedido->hora_instalacao ? 'às ' . substr($pedido->hora_instalacao, 0, 5) : '' }}</td>
            <td style="width: 25%;"><strong>Data Retirada:</strong></td>
            <td style="width: 25%;">{{ $pedido->data_retirada->format('d/m/Y') }} {{ $pedido->hora_retirada ? 'às ' . substr($pedido->hora_retirada, 0, 5) : '' }}</td>
        </tr>
        <tr>
            <td><strong>Montador / Técnico:</strong></td>
            <td>{{ $pedido->montador ? $pedido->montador->name : 'Equipe Técnica' }}</td>
            <td><strong>WhatsApp Montador:</strong></td>
            <td>{{ $pedido->montador->whatsapp ?? 'Não informado' }}</td>
        </tr>
    </table>

    <!-- Tendas e Materiais -->
    <div class="section-title">3. TENDAS E ITENS A INSTALAR</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 10%;">Item</th>
                <th style="width: 50%;">Descrição da Tenda</th>
                <th style="width: 25%;">Tamanho / Dimensões</th>
                <th style="width: 15%; text-align: center;">Quantidade</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pedido->tendas as $idx => $tenda)
                <tr>
                    <td style="text-align: center;">{{ $idx + 1 }}</td>
                    <td><strong>{{ $tenda->nome }}</strong> {{ $tenda->descricao ? ' - ' . $tenda->descricao : '' }}</td>
                    <td>{{ $tenda->tamanho }}</td>
                    <td style="text-align: center; font-weight: bold; font-size: 13px;">{{ $tenda->pivot->quantidade }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Assinaturas -->
    <div class="signature-box">
        <table style="width: 100%;">
            <tr>
                <td class="signature-line" style="float: left;">
                    <strong>{{ $pedido->cliente->nome }}</strong><br>
                    Assinatura do Cliente / Recebedor
                </td>
                <td style="width: 10%;"></td>
                <td class="signature-line" style="float: right;">
                    <strong>{{ $pedido->montador ? $pedido->montador->name : 'Técnico Responsável' }}</strong><br>
                    Assinatura do Montador
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        Documento gerado eletronicamente em {{ now()->format('d/m/Y H:i:s') }} - Sistema de Locação de Tendas
    </div>

</body>
</html>

