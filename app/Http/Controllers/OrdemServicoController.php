<?php

namespace App\Http\Controllers;

use App\Models\OrdemServico;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class OrdemServicoController extends Controller
{
    public function pdf(OrdemServico $ordemServico): Response
    {
        $ordemServico->load(['pedido.cliente', 'pedido.montador', 'pedido.tendas']);

        $pdf = Pdf::loadView('pdf.ordem_servico', [
            'os' => $ordemServico,
            'pedido' => $ordemServico->pedido,
        ])->setPaper('a4', 'portrait');

        $nomeArquivo = "{$ordemServico->numero_os}.pdf";

        return $pdf->stream($nomeArquivo);
    }
}

