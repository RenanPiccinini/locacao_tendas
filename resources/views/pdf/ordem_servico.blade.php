<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Ordem de Serviço - {{ $os->numero_os }}</title>
    <style>
        @page {
            margin: 12mm 15mm 12mm 15mm;
            size: A4 portrait;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            color: #000;
            font-size: 11px;
            line-height: 1.35;
            margin: 0;
            padding: 0;
        }

        .top-meta {
            width: 100%;
            margin-bottom: 4px;
        }
        .top-meta table {
            width: 100%;
            border-collapse: collapse;
        }
        .top-meta td {
            font-size: 10px;
            color: #333;
        }

        .header-logo {
            text-align: center;
            margin-bottom: 10px;
        }
        .header-logo img {
            height: 52px;
            margin: 0 auto 2px auto;
            display: block;
        }
        .header-logo .brand-name {
            font-size: 11px;
            font-weight: bold;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .header-logo .brand-sub {
            font-size: 9px;
            color: #444;
            letter-spacing: 0.6px;
            text-transform: uppercase;
        }

        /* Estrutura das Caixas idêntica ao modelo da foto */
        .os-box {
            width: 100%;
            border-collapse: collapse;
            border: 1.5px solid #333;
            margin-bottom: 8px;
        }
        .os-box td, .os-box th {
            font-size: 11px;
            color: #000;
            vertical-align: middle;
        }

        .box-title-center {
            text-align: center;
            font-weight: bold;
            font-size: 12px;
            padding: 5px 8px;
            border-bottom: 1.5px solid #333;
            letter-spacing: 0.5px;
        }
        .box-title-left {
            text-align: left;
            font-weight: bold;
            font-size: 11px;
            padding: 4px 8px;
            border-bottom: 1px solid #333;
        }

        .border-right {
            border-right: 1.5px solid #333;
        }

        /* Box Período(s) */
        .periodo-col {
            padding: 6px 8px;
            font-size: 10.5px;
            vertical-align: top;
        }

        /* Box Produtos */
        .th-prod {
            font-weight: bold;
            font-size: 11px;
            text-align: left;
            border-bottom: 1.5px solid #333;
            border-right: 1.5px solid #333;
            padding: 5px 8px;
        }
        .th-qtd {
            font-weight: bold;
            font-size: 11px;
            text-align: center;
            border-bottom: 1.5px solid #333;
            padding: 5px 8px;
        }
        .td-prod {
            border-bottom: 1px solid #ddd;
            border-right: 1.5px solid #333;
            padding: 5px 8px;
            font-size: 11px;
        }
        .td-qtd {
            border-bottom: 1px solid #ddd;
            text-align: center;
            padding: 5px 8px;
            font-size: 11px;
        }
        .last-row .td-prod, .last-row .td-qtd {
            border-bottom: none;
        }

        /* Box Observações */
        .obs-content {
            padding: 6px 8px;
            min-height: 48px;
            font-size: 10.5px;
            vertical-align: top;
        }
    </style>
</head>
<body>

    @php
        $logoPath = public_path('images/logo.png');
        $logoBase64 = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : '';
        
        $dataEmissaoExtenso = \Carbon\Carbon::parse($os->created_at ?? now())->locale('pt_BR')->translatedFormat('d \d\e F \d\e Y');
        
        $identificacao = $pedido->observacoes 
            ?: ($pedido->cliente->nome . ' ' . ($pedido->data_evento ? $pedido->data_evento->format('d/m/Y') : $pedido->data_instalacao->format('d/m/Y')));

        $solicitante = $pedido->nome_responsavel ?: $pedido->cliente->nome;
        $celularSolicitante = $pedido->telefone_responsavel ?: $pedido->cliente->telefone;
        $emailCliente = $pedido->cliente->email ?? '';

        $numApenas = preg_replace('/^OS[A-Z]*-([0-9]{4})-0*/i', '', $os->numero_os);
        $exibirNumero = $numApenas ?: ($os->id ?: $os->numero_os);
    @endphp

    <!-- Topo: Horário de Impressão e Nome do Sistema -->
    <div class="top-meta">
        <table>
            <tr>
                <td style="text-align: left; width: 50%;">{{ now()->format('d/m/y, H:i') }}</td>
                <td style="text-align: right; width: 50%;">Tendas Eldorado</td>
            </tr>
        </table>
    </div>

    <!-- Logotipo Centralizado -->
    <div class="header-logo">
        @if($logoBase64)
            <img src="data:image/png;base64,{{ $logoBase64 }}" alt="Logo Tendas Eldorado">
        @endif
        <div class="brand-name">Tendas Eldorado</div>
        <div class="brand-sub">Venda e Locação</div>
    </div>

    <!-- BOX 1: ORDEM DE SERVIÇO Nº -->
    <table class="os-box">
        <tr>
            <td class="box-title-center">
                ORDEM DE SERVIÇO Nº {{ $exibirNumero }}
            </td>
        </tr>
        <tr>
            <td style="padding: 6px 8px;">
                <div style="margin-bottom: 4px;">{{ $dataEmissaoExtenso }}</div>
                <div><strong>Identificação:</strong> {{ $identificacao }}</div>
            </td>
        </tr>
    </table>

    <!-- BOX 2: CLIENTE -->
    <table class="os-box">
        <tr>
            <td class="box-title-left">Cliente:</td>
        </tr>
        <tr>
            <td style="padding: 6px 8px;">
                <div style="margin-bottom: 4px;">
                    <strong>Razão Social:</strong> {{ $pedido->cliente->nome }}
                    <span style="margin: 0 4px;">-</span>
                    <strong>Fantasia:</strong> {{ $pedido->cliente->nome_fantasia ?: $pedido->cliente->nome }}
                </div>
                <div>
                    <strong>Solicitante:</strong> {{ $solicitante }}
                    <span style="margin: 0 4px;">-</span>
                    <strong>Celular:</strong> {{ $celularSolicitante }}
                    <span style="margin: 0 4px;">-</span>
                    <strong>Email:</strong> {{ $emailCliente }}
                </div>
            </td>
        </tr>
    </table>

    <!-- BOX 3: SAÍDA/RETORNO -->
    <table class="os-box">
        <tr>
            <td class="box-title-center">Saída/Retorno</td>
        </tr>
        <tr>
            <td style="padding: 6px 8px;">
                <strong>Descrição:</strong> {{ $pedido->observacoes ? 'Local de Montagem' : 'Local de Montagem' }}
                <span style="margin: 0 4px;">-</span>
                <strong>Endereço:</strong> {{ $pedido->endereco_instalacao }}{{ $pedido->cidade_instalacao ? ' - ' . $pedido->cidade_instalacao : '' }}
            </td>
        </tr>
    </table>

    <!-- BOX 4: PERÍODO(S) -->
    <table class="os-box">
        <tr>
            <td colspan="3" class="box-title-center">Período(s)</td>
        </tr>
        <tr>
            <td class="periodo-col border-right" style="width: 33.33%;">
                <strong>Saída:</strong> {{ $pedido->data_instalacao->format('d/m/Y') }} {{ $pedido->hora_instalacao ? substr($pedido->hora_instalacao, 0, 5) : '08:00' }}
            </td>
            <td class="periodo-col border-right" style="width: 33.34%;">
                <strong>Data do Evento:</strong> {{ $pedido->data_evento ? $pedido->data_evento->format('d/m/Y') : $pedido->data_instalacao->format('d/m/Y') }} {{ $pedido->hora_instalacao ? substr($pedido->hora_instalacao, 0, 5) : '08:00' }}
            </td>
            <td class="periodo-col" style="width: 33.33%;">
                <strong>Retorno:</strong> {{ $pedido->data_retirada->format('d/m/Y') }} {{ $pedido->hora_retirada ? substr($pedido->hora_retirada, 0, 5) : '08:00' }}
            </td>
        </tr>
    </table>

    <!-- BOX 5: PRODUTOS E QUANTIDADE -->
    <table class="os-box">
        <thead>
            <tr>
                <th class="th-prod" style="width: 72%;">Produto(s)</th>
                <th class="th-qtd" style="width: 28%;">Quantidade</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pedido->tendas as $tenda)
                <tr class="{{ $loop->last ? 'last-row' : '' }}">
                    <td class="td-prod">
                        TENDA - {{ strtoupper($tenda->tamanho) }} {{ $tenda->nome != $tenda->tamanho ? strtoupper($tenda->nome) : '' }}
                    </td>
                    <td class="td-qtd">
                        {{ $tenda->pivot->quantidade }} PEÇA{{ $tenda->pivot->quantidade > 1 ? 'S' : '' }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- BOX 6: OBSERVAÇÕES -->
    <table class="os-box">
        <tr>
            <td class="box-title-left">Observações:</td>
        </tr>
        <tr>
            <td class="obs-content">
                @if($pedido->observacoes)
                    <div>{{ $pedido->observacoes }}</div>
                @endif
                @if($os->observacoes_montador)
                    <div style="margin-top: 4px;"><strong>Obs. do Montador:</strong> {{ $os->observacoes_montador }}</div>
                @endif
                @if($pedido->montador)
                    <div style="margin-top: 4px; font-size: 10px; color: #444;">
                        <strong>Montador Responsável:</strong> {{ $pedido->montador->name }} (WhatsApp: {{ $pedido->montador->whatsapp ?? 'Não informado' }})
                    </div>
                @endif
            </td>
        </tr>
    </table>

</body>
</html>
