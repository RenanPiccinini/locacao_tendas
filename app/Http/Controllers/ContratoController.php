<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
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
     * Prepara os dados dinâmicos do contrato a partir do cliente e de suas locações
     */
    protected function prepararDadosContrato(Cliente $cliente): array
    {
        Carbon::setLocale('pt_BR');
        $pedido = $cliente->pedidos()->with('tendas')->latest()->first();

        $numeroContrato = $pedido ? sprintf('%04d', $pedido->id) : sprintf('%04d', $cliente->id);
        $dataExtenso = Carbon::now()->translatedFormat('d \d\e F \d\e Y');

        $itens = [];
        if ($pedido && $pedido->tendas->isNotEmpty()) {
            foreach ($pedido->tendas as $t) {
                $qtd = $t->pivot->quantidade ?? 1;
                $subtotal = $t->valor_diaria > 0 ? ($t->valor_diaria * $qtd) : 0;
                $itens[] = [
                    'nome' => $t->nome,
                    'tamanho' => $t->tamanho,
                    'quantidade' => $qtd,
                    'total' => $subtotal > 0 ? number_format($subtotal, 2, ',', '.') : ($pedido->valor_total ? number_format($pedido->valor_total, 2, ',', '.') : 'A combinar'),
                ];
            }
        } else {
            $itens[] = [
                'nome' => 'TENDA PIRAMIDAL',
                'tamanho' => 'Conforme especificação',
                'quantidade' => 1,
                'total' => $pedido && $pedido->valor_total ? number_format($pedido->valor_total, 2, ',', '.') : 'A combinar',
            ];
        }

        $saida = $pedido 
            ? Carbon::parse($pedido->data_instalacao)->format('d/m/Y') . ($pedido->hora_instalacao ? ' ' . substr($pedido->hora_instalacao, 0, 5) : ' 09:00')
            : Carbon::now()->format('d/m/Y') . ' 09:00';

        $inicio = $pedido 
            ? Carbon::parse($pedido->data_instalacao)->format('d/m/Y') . ' 10:00'
            : Carbon::now()->format('d/m/Y') . ' 10:00';

        $fim = $pedido 
            ? Carbon::parse($pedido->data_retirada)->format('d/m/Y') . ' 23:00'
            : Carbon::now()->addDays(2)->format('d/m/Y') . ' 23:00';

        $retorno = $pedido 
            ? Carbon::parse($pedido->data_retirada)->format('d/m/Y') . ($pedido->hora_retirada ? ' ' . substr($pedido->hora_retirada, 0, 5) : ' 17:00')
            : Carbon::now()->addDays(2)->format('d/m/Y') . ' 17:00';

        $vencimento = $pedido 
            ? Carbon::parse($pedido->data_instalacao)->format('d/m/Y')
            : Carbon::now()->format('d/m/Y');

        $valorTotal = $pedido && $pedido->valor_total 
            ? number_format($pedido->valor_total, 2, ',', '.') 
            : '0,00';

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
            'valor_total' => $valorTotal,
            'observacoes' => $observacoes,
            'pedido' => $pedido,
        ];
    }

    /**
     * Gera o contrato em PDF para o cliente e salva no storage
     */
    public function gerar(Cliente $cliente): JsonResponse
    {
        try {
            $dados = $this->prepararDadosContrato($cliente);
            
            $pdf = Pdf::loadView('pdf.contrato', compact('cliente', 'dados'));
            $pdf->setPaper('a4', 'portrait');

            $caminhoRelativo = "contratos/contrato_cliente_{$cliente->id}.pdf";
            Storage::disk('public')->put($caminhoRelativo, $pdf->output());

            $cliente->update([
                'contrato_path' => $caminhoRelativo,
                'contrato_gerado_em' => now(),
            ]);

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
     * Visualiza o contrato PDF inline no navegador
     */
    public function ver(Cliente $cliente): Response
    {
        $caminhoRelativo = "contratos/contrato_cliente_{$cliente->id}.pdf";

        if (!Storage::disk('public')->exists($caminhoRelativo)) {
            $dados = $this->prepararDadosContrato($cliente);
            $pdf = Pdf::loadView('pdf.contrato', compact('cliente', 'dados'));
            $pdf->setPaper('a4', 'portrait');
            Storage::disk('public')->put($caminhoRelativo, $pdf->output());

            $cliente->update([
                'contrato_path' => $caminhoRelativo,
                'contrato_gerado_em' => now(),
            ]);
        }

        $fileName = 'Contrato_Tendas_Eldorado_' . Str::slug($cliente->nome) . '.pdf';

        return Storage::disk('public')->response($caminhoRelativo, $fileName, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    /**
     * Baixa o contrato em formato PDF
     */
    public function download(Cliente $cliente): Response
    {
        $caminhoRelativo = "contratos/contrato_cliente_{$cliente->id}.pdf";

        if (!Storage::disk('public')->exists($caminhoRelativo)) {
            $dados = $this->prepararDadosContrato($cliente);
            $pdf = Pdf::loadView('pdf.contrato', compact('cliente', 'dados'));
            $pdf->setPaper('a4', 'portrait');
            Storage::disk('public')->put($caminhoRelativo, $pdf->output());

            $cliente->update([
                'contrato_path' => $caminhoRelativo,
                'contrato_gerado_em' => now(),
            ]);
        }

        $fileName = 'Contrato_Tendas_Eldorado_' . Str::slug($cliente->nome) . '.pdf';

        return Storage::disk('public')->download($caminhoRelativo, $fileName, [
            'Content-Type' => 'application/pdf',
        ]);
    }
}
