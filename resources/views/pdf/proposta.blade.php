<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Proposta de Locação Nº {{ $proposta->numero }}</title>
    <style>
        @page {
            margin: 12mm 15mm 12mm 15mm;
            size: A4 portrait;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            color: #000;
            font-size: 10.5px;
            line-height: 1.4;
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

        /* Estrutura em Caixas Retangulares idêntica ao layout modelo */
        .box {
            width: 100%;
            border-collapse: collapse;
            border: 1.5px solid #333;
            margin-bottom: 8px;
        }
        .box td, .box th {
            font-size: 10.5px;
            color: #000;
            vertical-align: middle;
        }

        .box-title-center {
            text-align: center;
            font-weight: bold;
            font-size: 11.5px;
            padding: 5px 8px;
            border-bottom: 1.5px solid #333;
            letter-spacing: 0.3px;
        }
        .box-title-center-no-border {
            text-align: center;
            font-weight: bold;
            font-size: 11.5px;
            padding: 5px 8px;
            letter-spacing: 0.3px;
        }
        .box-title-left {
            text-align: left;
            font-weight: bold;
            font-size: 10.5px;
            padding: 4px 8px;
            border-bottom: 1px solid #333;
        }

        .border-right {
            border-right: 1.5px solid #333;
        }

        .info-body {
            padding: 6px 8px;
            line-height: 1.5;
        }

        /* Períodos */
        .periodo-col {
            padding: 5px 6px;
            font-size: 10px;
            vertical-align: middle;
        }

        /* Produtos */
        .th-col {
            font-weight: bold;
            font-size: 10.5px;
            text-align: center;
            padding: 5px 8px;
        }
        .th-bottom {
            border-bottom: 1.5px solid #333;
        }
        .td-prod {
            border-bottom: 1px solid #ddd;
            border-right: 1.5px solid #333;
            padding: 5px 8px;
            text-align: left;
        }
        .td-qtd {
            border-bottom: 1px solid #ddd;
            border-right: 1.5px solid #333;
            padding: 5px 8px;
            text-align: center;
        }
        .td-valor {
            border-bottom: 1px solid #ddd;
            padding: 5px 8px;
            text-align: center;
        }
        .last-row td {
            border-bottom: none;
        }

        /* Totais */
        .total-col {
            padding: 5px 8px;
            text-align: center;
            font-size: 10.5px;
        }

        /* Observações */
        .obs-cell {
            padding: 7px 8px;
            height: 54px;
            vertical-align: top;
            font-size: 10.5px;
        }

        /* Assinaturas */
        .signatures {
            width: 100%;
            margin-top: 34px;
            border-collapse: collapse;
            page-break-inside: avoid;
        }
        .signatures td {
            width: 50%;
            text-align: center;
            vertical-align: top;
            padding: 0 12px;
        }
        .sig-line {
            border-top: 1px dashed #333;
            width: 82%;
            margin: 0 auto 5px auto;
        }
        .sig-label {
            font-weight: bold;
            font-size: 10.5px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
    </style>
</head>
<body>

    @php
        $logoPath = public_path('images/logo.png');
        $logoBase64 = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : '';

        $dataPropostaExtenso = $proposta->data_proposta
            ? \Carbon\Carbon::parse($proposta->data_proposta)->locale('pt_BR')->translatedFormat('d \d\e F \d\e Y')
            : now()->locale('pt_BR')->translatedFormat('d \d\e F \d\e Y');

        $formatarDataHora = function ($data, $hora) {
            if (!$data) {
                return '';
            }
            $str = \Carbon\Carbon::parse($data)->format('d/m/Y');
            if (!empty($hora)) {
                $str .= ' ' . substr((string) $hora, 0, 5);
            }
            return $str;
        };

        $saidaStr = $formatarDataHora($proposta->data_saida, $proposta->hora_saida);
        $inicioStr = $formatarDataHora($proposta->data_inicio, $proposta->hora_inicio);
        $fimStr = $formatarDataHora($proposta->data_fim, $proposta->hora_fim);
        $retornoStr = $formatarDataHora($proposta->data_retorno, $proposta->hora_retorno);

        $temPagamentos = !empty(trim((string) $proposta->pagamentos));
        $temObservacoes = !empty(trim((string) $proposta->observacoes));
        $temIdentificacao = !empty(trim((string) $proposta->identificacao));
        $temItens = $proposta->itens->isNotEmpty();
        $totalGeral = (float) ($proposta->valor_total_geral ?: $proposta->valor_total);
        $temTotalGeral = $totalGeral > 0;

        $renderLinhaCampos = function (array $campos): string {
            $partes = [];
            foreach ($campos as $label => $valor) {
                $texto = trim((string) ($valor ?? ''));
                if ($texto !== '') {
                    $partes[] = '<strong>' . e($label) . ':</strong> ' . e($texto);
                }
            }

            return implode(' - ', $partes);
        };

        $linhasDe = array_values(array_filter([
            $renderLinhaCampos([
                'Razão Social' => $proposta->de_razao_social,
                'Fantasia' => $proposta->de_fantasia,
            ]),
            $renderLinhaCampos([
                'CNPJ' => $proposta->de_cnpj,
                'IE' => $proposta->de_ie,
                'IM' => $proposta->de_im,
            ]),
            $renderLinhaCampos([
                'CEP' => $proposta->de_cep,
                'Endereço' => $proposta->de_endereco,
                'Bairro' => $proposta->de_bairro,
                'Cidade' => $proposta->de_cidade,
                'UF' => $proposta->de_uf,
            ]),
            $renderLinhaCampos([
                'Consultor' => $proposta->de_consultor,
                'Celular' => $proposta->de_celular,
                'Email' => $proposta->de_email,
            ]),
        ]));

        $linhasPara = array_values(array_filter([
            $renderLinhaCampos([
                'Razão Social' => $proposta->para_razao_social,
                'Fantasia' => $proposta->para_fantasia,
            ]),
            $renderLinhaCampos([
                'CNPJ' => $proposta->para_cnpj_cpf,
                'Inscrição Estadual' => $proposta->para_ie,
            ]),
            $renderLinhaCampos([
                'CEP' => $proposta->para_cep,
                'Endereço' => $proposta->para_endereco,
                'Bairro' => $proposta->para_bairro,
                'Cidade' => $proposta->para_cidade,
                'UF' => $proposta->para_uf,
            ]),
            $renderLinhaCampos([
                'Solicitante' => $proposta->para_solicitante,
                'Celular' => $proposta->para_celular,
                'Email' => $proposta->para_email,
            ]),
        ]));
    @endphp

    <!-- Topo: Data/Hora e Sistema -->
    <div class="top-meta">
        <table>
            <tr>
                <td style="text-align: left; width: 50%;">{{ ($proposta->created_at ?? now())->timezone('America/Sao_Paulo')->format('d/m/y, H:i') }}</td>
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

    <!-- BOX 1: PROPOSTA DE LOCAÇÃO Nº -->
    <table class="box">
        <tr>
            <td class="box-title-center">
                PROPOSTA DE LOCAÇÃO Nº {{ $proposta->numero }}
            </td>
        </tr>
        <tr>
            <td class="info-body">
                <div style="{{ $temIdentificacao ? 'margin-bottom: 2px;' : '' }}">{{ $dataPropostaExtenso }}</div>
                @if($temIdentificacao)
                    <div><strong>Identificação:</strong> {{ $proposta->identificacao }}</div>
                @endif
            </td>
        </tr>
    </table>

    @if(!empty($linhasDe))
        <!-- BOX 2: DE -->
        <table class="box">
            <tr>
                <td class="box-title-left">De:</td>
            </tr>
            <tr>
                <td class="info-body">
                    @foreach($linhasDe as $linha)
                        <div>{!! $linha !!}</div>
                    @endforeach
                </td>
            </tr>
        </table>
    @endif

    @if(!empty($linhasPara))
        <!-- BOX 3: PARA -->
        <table class="box">
            <tr>
                <td class="box-title-left">Para:</td>
            </tr>
            <tr>
                <td class="info-body">
                    @foreach($linhasPara as $linha)
                        <div>{!! $linha !!}</div>
                    @endforeach
                </td>
            </tr>
        </table>
    @endif

    @php
        $dataEventoInicio = $proposta->data_inicio
            ? \Carbon\Carbon::parse($proposta->data_inicio)->format('d/m/Y')
            : ($proposta->data_saida ? \Carbon\Carbon::parse($proposta->data_saida)->format('d/m/Y') : null);

        $dataEventoFim = $proposta->data_fim
            ? \Carbon\Carbon::parse($proposta->data_fim)->format('d/m/Y')
            : ($proposta->data_retorno ? \Carbon\Carbon::parse($proposta->data_retorno)->format('d/m/Y') : null);

        $dataAssinaturaAutomatica = $proposta->data_proposta
            ? \Carbon\Carbon::parse($proposta->data_proposta)->format('d/m/Y')
            : ($proposta->created_at ? $proposta->created_at->format('d/m/Y') : now()->format('d/m/Y'));
    @endphp

    @if($dataEventoInicio || $dataEventoFim)
        <!-- BOX 4: DATA DO EVENTO -->
        <table class="box">
            <tr>
                <td colspan="{{ ($dataEventoInicio && $dataEventoFim) ? 2 : 1 }}" class="box-title-center">Data do Evento</td>
            </tr>
            <tr>
                @if($dataEventoInicio && $dataEventoFim)
                    <td class="periodo-col border-right" style="width: 50%; text-align: center; font-size: 11px;">
                        <strong>De:</strong> {{ $dataEventoInicio }}
                    </td>
                    <td class="periodo-col" style="width: 50%; text-align: center; font-size: 11px;">
                        <strong>Até:</strong> {{ $dataEventoFim }}
                    </td>
                @elseif($dataEventoInicio)
                    <td class="periodo-col" style="width: 100%; text-align: center; font-size: 11px;">
                        <strong>De:</strong> {{ $dataEventoInicio }}
                    </td>
                @else
                    <td class="periodo-col" style="width: 100%; text-align: center; font-size: 11px;">
                        <strong>Até:</strong> {{ $dataEventoFim }}
                    </td>
                @endif
            </tr>
        </table>
    @endif

    @if($temItens)
        <!-- BOX 5: PRODUTO(S) / QTD / VALOR/PERÍODO -->
        <table class="box">
            <thead>
                <tr>
                    <th class="th-col border-right th-bottom" style="width: 52%;">Produto(s)</th>
                    <th class="th-col border-right th-bottom" style="width: 14%;">Qtd</th>
                    <th class="th-col th-bottom" style="width: 34%;">Valor/Período</th>
                </tr>
            </thead>
            <tbody>
                @foreach($proposta->itens as $item)
                    <tr class="{{ $loop->last ? 'last-row' : '' }}">
                        <td class="td-prod">{{ $item->descricao }}</td>
                        <td class="td-qtd">{{ $item->quantidade }}</td>
                        <td class="td-valor">R$ {{ number_format((float) $item->valor_periodo, 2, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if($temTotalGeral)
        <!-- BOX 6: TOTAL GERAL -->
        <table class="box">
            <tr>
                <td class="total-col" style="width: 100%; text-align: center; font-size: 11.5px;">
                    <strong>Total Geral:</strong> R$ {{ number_format($totalGeral, 2, ',', '.') }}
                </td>
            </tr>
        </table>
    @endif

    @if($temPagamentos)
        <!-- BOX 7: PAGAMENTO(S) -->
        <table class="box">
            <tr>
                <td class="box-title-center">Pagamento(s)</td>
            </tr>
            <tr>
                <td class="info-body">{!! nl2br(e($proposta->pagamentos)) !!}</td>
            </tr>
        </table>
    @endif

    @if($temObservacoes)
        <!-- BOX 8: OBSERVAÇÕES -->
        <table class="box">
            <tr>
                <td class="obs-cell">
                    <div style="margin-bottom: 4px;"><strong>Observações:</strong></div>
                    <div>{!! nl2br(e($proposta->observacoes)) !!}</div>
                </td>
            </tr>
        </table>
    @endif

    <!-- RODAPÉ DE ASSINATURA -->
    <table class="signatures">
        <tr>
            <td style="vertical-align: top;">
                <div style="font-size: 11px; font-weight: bold; margin-bottom: 5px; color: #111; height: 22px; line-height: 22px;">
                    {{ $dataAssinaturaAutomatica }}
                </div>
                <div class="sig-line"></div>
                <div class="sig-label">DATA</div>
            </td>
            <td style="vertical-align: top;">
                <div style="font-family: 'Times New Roman', Times, serif; font-style: italic; font-size: 14px; font-weight: normal; margin-bottom: 5px; color: #111; height: 22px; line-height: 22px;">
                    Marcia Regina Sabino Gibertoni
                </div>
                <div class="sig-line"></div>
                <div class="sig-label">ASSINATURA</div>
            </td>
        </tr>
    </table>

    <!-- VALIDADE DA PROPOSTA -->
    <div style="margin-top: 22px; text-align: center; font-size: 10.5px; font-weight: bold; color: #222; text-transform: uppercase; letter-spacing: 0.5px;">
        Validade da proposta: 30 dias
    </div>

</body>
</html>
