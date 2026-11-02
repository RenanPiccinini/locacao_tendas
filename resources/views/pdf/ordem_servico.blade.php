<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Ordem de Serviço - {{ $os->numero_os }}</title>
    @php
        $corTema = match($os->tipo) {
            'instalacao' => '#2563eb', // Azul
            'retirada' => '#7c3aed',   // Roxo
            'conclusao' => '#059669',  // Verde
            'manutencao' => '#d97706', // Âmbar
            default => '#1e3a8a',
        };
        $bgBadge = match($os->tipo) {
            'instalacao' => '#dbeafe',
            'retirada' => '#f3e8ff',
            'conclusao' => '#d1fae5',
            'manutencao' => '#fef3c7',
            default => '#e2e8f0',
        };
        $textoBadge = match($os->tipo) {
            'instalacao' => '#1e40af',
            'retirada' => '#6b21a8',
            'conclusao' => '#065f46',
            'manutencao' => '#92400e',
            default => '#1e293b',
        };
        $tituloItens = match($os->tipo) {
            'instalacao' => '3. TENDAS E ITENS A INSTALAR NO LOCAL',
            'retirada' => '3. TENDAS E ITENS A DESMONTAR E RECOLHER',
            'conclusao' => '3. TENDAS DEVOLVIDAS E REINTEGRADAS AO ESTOQUE',
            'manutencao' => '3. TENDAS E ITENS VISTORIADOS / EM MANUTENÇÃO',
            default => '3. TENDAS E MATERIAIS',
        };
    @endphp
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
            border-bottom: 2px solid {{ $corTema }};
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header table {
            width: 100%;
        }
        .title {
            font-size: 20px;
            font-weight: bold;
            color: {{ $corTema }};
        }
        .os-number {
            font-size: 16px;
            font-weight: bold;
            color: {{ $corTema }};
            text-align: right;
        }
        .badge {
            display: inline-block;
            padding: 4px 8px;
            background-color: {{ $bgBadge }};
            color: {{ $textoBadge }};
            font-weight: bold;
            border-radius: 4px;
            text-transform: uppercase;
            font-size: 10px;
        }
        .badge-status {
            display: inline-block;
            padding: 2px 6px;
            background-color: #f1f5f9;
            color: #475569;
            font-size: 9px;
            font-weight: bold;
            border-radius: 3px;
            text-transform: uppercase;
        }
        .section-title {
            background-color: #f8fafc;
            padding: 6px 10px;
            font-weight: bold;
            font-size: 12px;
            color: #0f172a;
            margin-top: 15px;
            margin-bottom: 8px;
            border-left: 3px solid {{ $corTema }};
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
            margin-top: 45px;
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
            margin-top: 35px;
            text-align: center;
            font-size: 10px;
            color: #64748b;
            border-top: 1px solid #e2e8f0;
            padding-top: 8px;
        }
    </style>
</head>
<body>

    <div class="header">
        <table>
            <tr>
                <td>
                    <div class="title">TENDAS ELDORADO</div>
                    <div style="color: #64748b; font-size: 11px; margin-top: 2px;">Sistema Operacional de Locações & Montagens</div>
                </td>
                <td class="os-number">
                    ORDEM DE SERVIÇO<br>
                    <span>{{ $os->numero_os }}</span><br>
                    <span class="badge">{{ mb_strtoupper($os->tipo_label) }}</span>
                    <div style="font-size: 10px; margin-top: 4px; color: #475569;">
                        Status: <span class="badge-status">{{ $os->status_label }}</span>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Dados do Cliente e Local -->
    <div class="section-title">1. DADOS DO CLIENTE E LOCAL DE ATENDIMENTO</div>
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
        @if($os->observacoes_montador)
        <tr>
            <td><strong>Obs. do Montador:</strong></td>
            <td colspan="3" style="color: {{ $corTema }};"><strong>{{ $os->observacoes_montador }}</strong></td>
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
        <tr>
            <td><strong>Emissão da O.S.:</strong></td>
            <td>{{ $os->created_at->format('d/m/Y H:i') }}</td>
            <td><strong>Status / Conclusão:</strong></td>
            <td>
                {{ $os->status_label }}
                @if($os->concluido_em)
                    (em {{ $os->concluido_em->format('d/m/Y H:i') }})
                @endif
            </td>
        </tr>
    </table>

    <!-- Tendas e Materiais -->
    <div class="section-title">{{ $tituloItens }}</div>
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

    <!-- Assinaturas de Conferência -->
    <div class="signature-box">
        <table style="width: 100%;">
            <tr>
                <td class="signature-line" style="float: left;">
                    <strong>{{ $pedido->cliente->nome }}</strong><br>
                    @if($os->tipo === 'instalacao')
                        Assinatura do Cliente / Recebimento da Montagem
                    @elseif($os->tipo === 'retirada')
                        Assinatura do Cliente / Liberação da Retirada
                    @else
                        Assinatura do Cliente / Encerramento do Pedido
                    @endif
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
        Documento gerado eletronicamente em {{ now()->format('d/m/Y H:i:s') }} - Sistema de Locação Tendas Eldorado | Pedido #{{ $pedido->id }}
    </div>

</body>
</html>
