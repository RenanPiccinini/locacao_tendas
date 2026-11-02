<?php

namespace App\Http\Controllers;

use App\Models\OrdemServico;
use App\Services\WhatsappService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
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

    public function download(OrdemServico $ordemServico): Response
    {
        $ordemServico->load(['pedido.cliente', 'pedido.montador', 'pedido.tendas']);

        $pdf = Pdf::loadView('pdf.ordem_servico', [
            'os' => $ordemServico,
            'pedido' => $ordemServico->pedido,
        ])->setPaper('a4', 'portrait');

        $nomeArquivo = "{$ordemServico->numero_os}.pdf";

        return $pdf->download($nomeArquivo);
    }

    public function enviarWhatsapp(OrdemServico $ordemServico, WhatsappService $whatsapp): RedirectResponse
    {
        $pedido = $ordemServico->pedido;
        $pedido->loadMissing(['montador', 'cliente', 'tendas']);

        if (!$pedido->montador || empty($pedido->montador->whatsapp)) {
            return back()->with('error', 'O montador deste pedido não possui telefone/WhatsApp cadastrado.');
        }

        $dataInst = Carbon::parse($pedido->data_instalacao)->format('d/m/Y');
        $horaInst = $pedido->hora_instalacao ? substr($pedido->hora_instalacao, 0, 5) : 'A combinar';
        $dataRet = Carbon::parse($pedido->data_retirada)->format('d/m/Y');
        $horaRet = $pedido->hora_retirada ? substr($pedido->hora_retirada, 0, 5) : 'A combinar';

        $tendasLista = '';
        foreach ($pedido->tendas as $t) {
            $tendasLista .= "• {$t->pivot->quantidade}x {$t->nome} ({$t->tamanho})\n";
        }

        $msg = "📋 *ORDEM DE SERVIÇO: {$ordemServico->numero_os}*\n"
             . "📌 *Tipo:* {$ordemServico->tipo_label}\n"
             . "🔄 *Status:* {$ordemServico->status_label}\n\n"
             . "👤 *Cliente:* {$pedido->cliente->nome}\n"
             . "📞 *Telefone:* {$pedido->cliente->telefone}\n"
             . "📅 *Data de Instalação:* {$dataInst} às {$horaInst}\n"
             . "🔄 *Data de Retirada:* {$dataRet} às {$horaRet}\n"
             . "📍 *Local:* {$pedido->endereco_instalacao}" . ($pedido->cidade_instalacao ? " - {$pedido->cidade_instalacao}" : "") . "\n\n"
             . "⛺ *Tendas:*\n{$tendasLista}\n"
             . "📝 *Observações:* " . ($pedido->observacoes ?: 'Nenhuma observação.') . "\n\n"
             . "Acompanhe e registre os status diretamente pelo sistema!";

        $whatsapp->enviarMensagem($pedido->montador->whatsapp, $msg);

        return back()->with('success', "O.S. {$ordemServico->numero_os} ({$ordemServico->tipo_label}) enviada com sucesso para o WhatsApp de {$pedido->montador->name}!");
    }
}
