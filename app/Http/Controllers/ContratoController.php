<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Pedido;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class ContratoController extends Controller
{
    /**
     * Converte um valor numérico em reais por extenso
     */
    public function valorPorExtenso(float $valor): string
    {
        if ($valor <= 0) {
            return 'zero reais';
        }

        $inteiro = (int) floor($valor);
        $centavos = (int) round(($valor - $inteiro) * 100);

        $partes = [];

        if (class_exists(\NumberFormatter::class)) {
            $formatter = new \NumberFormatter('pt_BR', \NumberFormatter::SPELLOUT);

            if ($inteiro > 0) {
                $textoInteiro = $formatter->format($inteiro);
                $partes[] = $textoInteiro . ($inteiro == 1 ? ' real' : ' reais');
            }

            if ($centavos > 0) {
                $textoCentavos = $formatter->format($centavos);
                $partes[] = $textoCentavos . ($centavos == 1 ? ' centavo' : ' centavos');
            }
        } else {
            $partes[] = number_format($valor, 2, ',', '.') . ' reais';
        }

        return implode(' e ', $partes);
    }

    /**
     * Prepara os dados dinâmicos do contrato a partir do cliente e de suas locações/pedido específico
     */
    public function prepararDadosContrato(Cliente $cliente, ?Pedido $pedido = null): array
    {
        Carbon::setLocale('pt_BR');

        // Se o pedido não foi passado diretamente, tenta buscar pelo ID na requisição ou o mais recente
        if (!$pedido) {
            $pedidoId = request()->input('pedido_id') ?? request()->query('pedido_id');
            if ($pedidoId) {
                $pedido = $cliente->pedidos()->with(['tendas', 'montador'])->find($pedidoId);
            }
            if (!$pedido) {
                $pedido = $cliente->pedidos()->with(['tendas', 'montador'])->latest()->first();
            }
        } else {
            $pedido->loadMissing(['tendas', 'montador', 'cliente']);
            $cliente = $pedido->cliente ?? $cliente;
        }

        $numeroContrato = $pedido ? sprintf('%04d', $pedido->id) : sprintf('%04d', $cliente->id);
        $dataExtenso = $pedido && $pedido->created_at 
            ? Carbon::parse($pedido->created_at)->translatedFormat('d \d\e F \d\e Y')
            : Carbon::now()->translatedFormat('d \d\e F \d\e Y');

        $valorTotalNumerico = $pedido && $pedido->valor_total !== null ? (float) $pedido->valor_total : 0.0;

        // Duração do evento/locação em dias
        $dias = 1;
        if ($pedido && $pedido->data_instalacao && $pedido->data_retirada) {
            $dtIni = Carbon::parse($pedido->data_instalacao);
            $dtFim = Carbon::parse($pedido->data_retirada);
            $dias = max(1, $dtIni->diffInDays($dtFim));
        }

        $itens = [];
        if ($pedido && $pedido->tendas->isNotEmpty()) {
            $totalQtdGeral = $pedido->tendas->sum(fn($t) => $t->pivot->quantidade ?? 1);

            // Soma das diárias calculadas
            $somaDiarias = 0;
            foreach ($pedido->tendas as $t) {
                $qtd = $t->pivot->quantidade ?? 1;
                $somaDiarias += ((float)$t->valor_diaria * $qtd * $dias);
            }

            foreach ($pedido->tendas as $t) {
                $qtd = $t->pivot->quantidade ?? 1;

                if ((float)$t->valor_diaria > 0 && ($valorTotalNumerico <= 0 || abs($somaDiarias - $valorTotalNumerico) < 0.01)) {
                    $subtotal = (float)$t->valor_diaria * $qtd * $dias;
                } elseif ($valorTotalNumerico > 0) {
                    // Se houver um valor_total fechado, calcula proporcionalmente pela quantidade de peças
                    $subtotal = ($valorTotalNumerico / max(1, $totalQtdGeral)) * $qtd;
                } elseif ((float)$t->valor_diaria > 0) {
                    $subtotal = (float)$t->valor_diaria * $qtd * $dias;
                } else {
                    $subtotal = 0;
                }

                $itens[] = [
                    'nome' => $t->nome,
                    'tamanho' => $t->tamanho ?: 'Padrão',
                    'quantidade' => $qtd,
                    'valor_diaria' => (float)$t->valor_diaria > 0 ? number_format((float)$t->valor_diaria, 2, ',', '.') : null,
                    'total' => $subtotal > 0 ? number_format($subtotal, 2, ',', '.') : ($valorTotalNumerico > 0 ? number_format($valorTotalNumerico, 2, ',', '.') : 'A combinar'),
                ];
            }
        } else {
            $itens[] = [
                'nome' => 'TENDA PIRAMIDAL',
                'tamanho' => 'Conforme especificação do pedido',
                'quantidade' => 1,
                'valor_diaria' => null,
                'total' => $valorTotalNumerico > 0 ? number_format($valorTotalNumerico, 2, ',', '.') : 'A combinar',
            ];
        }

        // 1ª) Datas dinâmicas do pedido
        $saida = $pedido && $pedido->data_instalacao 
            ? Carbon::parse($pedido->data_instalacao)->format('d/m/Y') . ($pedido->hora_instalacao ? ' ' . substr($pedido->hora_instalacao, 0, 5) : ' 09:00')
            : Carbon::now()->format('d/m/Y') . ' 09:00';

        $inicio = $pedido && ($pedido->data_evento ?: $pedido->data_instalacao)
            ? Carbon::parse($pedido->data_evento ?: $pedido->data_instalacao)->format('d/m/Y') . ($pedido->hora_instalacao ? ' ' . substr($pedido->hora_instalacao, 0, 5) : ' 10:00')
            : Carbon::now()->format('d/m/Y') . ' 10:00';

        $fim = $pedido && ($pedido->data_evento ?: $pedido->data_retirada)
            ? Carbon::parse($pedido->data_evento ?: $pedido->data_retirada)->format('d/m/Y') . ($pedido->hora_retirada ? ' ' . substr($pedido->hora_retirada, 0, 5) : ' 23:00')
            : Carbon::now()->addDays(2)->format('d/m/Y') . ' 23:00';

        $retorno = $pedido && $pedido->data_retirada
            ? Carbon::parse($pedido->data_retirada)->format('d/m/Y') . ($pedido->hora_retirada ? ' ' . substr($pedido->hora_retirada, 0, 5) : ' 17:00')
            : Carbon::now()->addDays(2)->format('d/m/Y') . ' 17:00';

        // 2ª) Vencimento e Valores dinâmicos do pedido
        $vencimento = $pedido && $pedido->data_instalacao 
            ? Carbon::parse($pedido->data_instalacao)->format('d/m/Y')
            : Carbon::now()->format('d/m/Y');

        $valorTotalFormatado = number_format($valorTotalNumerico, 2, ',', '.');
        $valorTotalExtenso = $this->valorPorExtenso($valorTotalNumerico);

        // Informações de local e endereço da instalação específico do pedido
        $localInstalacao = null;
        if ($pedido && $pedido->endereco_instalacao) {
            $partesLocal = [$pedido->endereco_instalacao];
            if ($pedido->cidade_instalacao) {
                $partesLocal[] = $pedido->cidade_instalacao;
            }
            if ($pedido->cep_instalacao) {
                $partesLocal[] = 'CEP: ' . $pedido->cep_instalacao;
            }
            $localInstalacao = implode(' - ', $partesLocal);
        }

        $responsavelLocal = null;
        if ($pedido && ($pedido->nome_responsavel || $pedido->telefone_responsavel)) {
            $responsavelLocal = trim(($pedido->nome_responsavel ?? '') . ($pedido->telefone_responsavel ? " ({$pedido->telefone_responsavel})" : ''));
        }

        $montadorResponsavel = null;
        if ($pedido && $pedido->montador) {
            $montadorResponsavel = $pedido->montador->name . ($pedido->montador->whatsapp ? " ({$pedido->montador->whatsapp})" : '');
        }

        $observacoes = $pedido ? ($pedido->observacoes ?: $cliente->observacoes) : $cliente->observacoes;

        return [
            'numero_contrato' => $numeroContrato,
            'data_extenso' => $dataExtenso,
            'itens' => $itens,
            'saida' => $saida,
            'inicio' => $inicio,
            'fim' => $fim,
            'retorno' => $retorno,
            'vencimento' => $vencimento,
            'valor_total' => $valorTotalFormatado,
            'valor_total_extenso' => $valorTotalExtenso,
            'local_instalacao' => $localInstalacao,
            'responsavel_local' => $responsavelLocal,
            'montador_responsavel' => $montadorResponsavel,
            'observacoes' => $observacoes,
            'dias' => $dias,
            'pedido' => $pedido,
        ];
    }

    /**
     * Gera o contrato em PDF para o cliente (e pedido) e salva no storage
     */
    public function gerar(Cliente $cliente, Request $request): JsonResponse
    {
        try {
            $pedidoId = $request->input('pedido_id') ?? $request->query('pedido_id');
            $pedido = $pedidoId ? $cliente->pedidos()->find($pedidoId) : null;

            $dados = $this->prepararDadosContrato($cliente, $pedido);
            $pedidoUsado = $dados['pedido'];
            
            $pdf = Pdf::loadView('pdf.contrato', [
                'cliente' => $cliente,
                'dados' => $dados,
                'pedido' => $pedidoUsado,
            ]);
            $pdf->setPaper('a4', 'portrait');

            $caminhoRelativo = "contratos/contrato_cliente_{$cliente->id}.pdf";
            Storage::disk('public')->put($caminhoRelativo, $pdf->output());

            $cliente->update([
                'contrato_path' => $caminhoRelativo,
                'contrato_gerado_em' => now(),
            ]);

            if ($pedidoUsado) {
                $caminhoPedido = "contratos/contrato_pedido_{$pedidoUsado->id}.pdf";
                Storage::disk('public')->put($caminhoPedido, $pdf->output());
            }

            return response()->json([
                'success' => true,
                'message' => "Contrato do cliente {$cliente->nome} gerado com sucesso!",
                'url' => route('clientes.contrato.ver', $cliente),
                'download_url' => route('clientes.contrato.download', $cliente),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => "Erro ao gerar contrato: " . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Visualiza o contrato PDF inline no navegador com dados atualizados do pedido
     */
    public function ver(Cliente $cliente, Request $request): Response
    {
        $pedidoId = $request->input('pedido_id') ?? $request->query('pedido_id');
        $pedido = $pedidoId ? $cliente->pedidos()->find($pedidoId) : null;

        $dados = $this->prepararDadosContrato($cliente, $pedido);
        $pedidoUsado = $dados['pedido'];

        $pdf = Pdf::loadView('pdf.contrato', [
            'cliente' => $cliente,
            'dados' => $dados,
            'pedido' => $pedidoUsado,
        ]);
        $pdf->setPaper('a4', 'portrait');

        $caminhoRelativo = "contratos/contrato_cliente_{$cliente->id}.pdf";
        Storage::disk('public')->put($caminhoRelativo, $pdf->output());

        $cliente->update([
            'contrato_path' => $caminhoRelativo,
            'contrato_gerado_em' => now(),
        ]);

        $fileName = 'Contrato_Tendas_Eldorado_' . Str::slug($cliente->nome) . ($pedidoUsado ? '_Pedido_' . $pedidoUsado->id : '') . '.pdf';

        return $pdf->stream($fileName);
    }

    /**
     * Baixa o contrato em formato PDF com dados dinâmicos do pedido
     */
    public function download(Cliente $cliente, Request $request): Response
    {
        $pedidoId = $request->input('pedido_id') ?? $request->query('pedido_id');
        $pedido = $pedidoId ? $cliente->pedidos()->find($pedidoId) : null;

        $dados = $this->prepararDadosContrato($cliente, $pedido);
        $pedidoUsado = $dados['pedido'];

        $pdf = Pdf::loadView('pdf.contrato', [
            'cliente' => $cliente,
            'dados' => $dados,
            'pedido' => $pedidoUsado,
        ]);
        $pdf->setPaper('a4', 'portrait');

        $fileName = 'Contrato_Tendas_Eldorado_' . Str::slug($cliente->nome) . ($pedidoUsado ? '_Pedido_' . $pedidoUsado->id : '') . '.pdf';

        return $pdf->download($fileName);
    }

    /**
     * Visualiza o contrato gerado especificamente para um determinado Pedido
     */
    public function verPedido(Pedido $pedido): Response
    {
        $pedido->loadMissing(['cliente', 'tendas', 'montador']);
        $cliente = $pedido->cliente;

        $dados = $this->prepararDadosContrato($cliente, $pedido);

        $pdf = Pdf::loadView('pdf.contrato', [
            'cliente' => $cliente,
            'dados' => $dados,
            'pedido' => $pedido,
        ]);
        $pdf->setPaper('a4', 'portrait');

        // Salva cópia atualizada
        $caminhoPedido = "contratos/contrato_pedido_{$pedido->id}.pdf";
        Storage::disk('public')->put($caminhoPedido, $pdf->output());

        $fileName = 'Contrato_Tendas_Eldorado_Pedido_' . $pedido->id . '_' . Str::slug($cliente->nome) . '.pdf';

        return $pdf->stream($fileName);
    }

    /**
     * Baixa o contrato gerado especificamente para um determinado Pedido
     */
    public function downloadPedido(Pedido $pedido): Response
    {
        $pedido->loadMissing(['cliente', 'tendas', 'montador']);
        $cliente = $pedido->cliente;

        $dados = $this->prepararDadosContrato($cliente, $pedido);

        $pdf = Pdf::loadView('pdf.contrato', [
            'cliente' => $cliente,
            'dados' => $dados,
            'pedido' => $pedido,
        ]);
        $pdf->setPaper('a4', 'portrait');

        $fileName = 'Contrato_Tendas_Eldorado_Pedido_' . $pedido->id . '_' . Str::slug($cliente->nome) . '.pdf';

        return $pdf->download($fileName);
    }

    /**
     * Gera e salva o contrato de um pedido específico
     */
    public function gerarPedido(Pedido $pedido): JsonResponse
    {
        try {
            $pedido->loadMissing(['cliente', 'tendas', 'montador']);
            $cliente = $pedido->cliente;

            $dados = $this->prepararDadosContrato($cliente, $pedido);

            $pdf = Pdf::loadView('pdf.contrato', [
                'cliente' => $cliente,
                'dados' => $dados,
                'pedido' => $pedido,
            ]);
            $pdf->setPaper('a4', 'portrait');

            $caminhoPedido = "contratos/contrato_pedido_{$pedido->id}.pdf";
            Storage::disk('public')->put($caminhoPedido, $pdf->output());

            $cliente->update([
                'contrato_path' => $caminhoPedido,
                'contrato_gerado_em' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => "Contrato do Pedido #{$pedido->id} gerado com sucesso!",
                'url' => route('pedidos.contrato.ver', $pedido),
                'download_url' => route('pedidos.contrato.download', $pedido),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => "Erro ao gerar contrato: " . $e->getMessage(),
            ], 500);
        }
    }
}
