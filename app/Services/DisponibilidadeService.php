<?php

namespace App\Services;

use App\Models\Pedido;
use App\Models\Tenda;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class DisponibilidadeService
{
    /**
     * Consulta a disponibilidade das tendas e detalhes das locações para uma data ou período.
     *
     * @param string|Carbon|null $dataInicio
     * @param string|Carbon|null $dataFim
     * @return array
     */
    public function consultarDisponibilidade($dataInicio = null, $dataFim = null): array
    {
        $inicio = $dataInicio ? Carbon::parse($dataInicio)->startOfDay() : Carbon::today()->startOfDay();
        $fim = $dataFim ? Carbon::parse($dataFim)->endOfDay() : $inicio->copy()->endOfDay();

        if ($fim->lt($inicio)) {
            $fim = $inicio->copy()->endOfDay();
        }

        $inicioStr = $inicio->format('Y-m-d');
        $fimStr = $fim->format('Y-m-d');

        // Pedidos ativos cuja locação sobrepõe o período consultado
        $pedidosAtivos = Pedido::with(['cliente', 'montador', 'tendas'])
            ->whereNotIn('status', ['cancelado', 'concluido'])
            ->whereDate('data_instalacao', '<=', $fimStr)
            ->whereDate('data_retirada', '>=', $inicioStr)
            ->orderBy('data_retirada', 'asc')
            ->orderBy('hora_retirada', 'asc')
            ->get();

        // Lista de todas as tendas cadastradas com disponibilidade calculada para a data
        $todasTendas = Tenda::orderBy('nome')->get();
        $tendasDisponibilidade = $todasTendas->map(function ($tenda) use ($pedidosAtivos) {
            $emLocacao = 0;
            foreach ($pedidosAtivos as $pedido) {
                $item = $pedido->tendas->firstWhere('id', $tenda->id);
                if ($item) {
                    $emLocacao += (int) $item->pivot->quantidade;
                }
            }

            $disponivel = max(0, $tenda->quantidade_total - $emLocacao);

            $tenda->quantidade_em_locacao = $emLocacao;
            $tenda->quantidade_disponivel_periodo = $disponivel;
            return $tenda;
        });

        // Detalhamento item a item das tendas em locação no período com previsão de retorno
        $itensEmLocacao = collect();
        foreach ($pedidosAtivos as $pedido) {
            foreach ($pedido->tendas as $tendaPivot) {
                $itensEmLocacao->push([
                    'pedido_id' => $pedido->id,
                    'pedido' => $pedido,
                    'tenda_id' => $tendaPivot->id,
                    'tenda_nome' => $tendaPivot->nome,
                    'tenda_tamanho' => $tendaPivot->tamanho,
                    'quantidade' => (int) $tendaPivot->pivot->quantidade,
                    'cliente_nome' => $pedido->cliente->nome ?? 'Cliente não informado',
                    'cliente_telefone' => $pedido->cliente->telefone ?? '',
                    'montador_nome' => $pedido->montador ? $pedido->montador->name : 'Montador não definido',
                    'montador_whatsapp' => $pedido->montador->whatsapp ?? null,
                    'data_instalacao' => $pedido->data_instalacao,
                    'hora_instalacao' => $pedido->hora_instalacao,
                    'data_retirada' => $pedido->data_retirada,
                    'hora_retirada' => $pedido->hora_retirada,
                    'endereco' => $pedido->endereco_instalacao,
                    'cidade' => $pedido->cidade_instalacao,
                    'status' => $pedido->status,
                    'status_label' => $pedido->status_label,
                    'status_color' => $pedido->status_color,
                ]);
            }
        }

        $isMesmoDia = $inicio->isSameDay($fim);

        return [
            'data_inicio' => $inicio->format('Y-m-d'),
            'data_fim' => $fim->format('Y-m-d'),
            'is_mesmo_dia' => $isMesmoDia,
            'data_formatada' => $isMesmoDia 
                ? $inicio->format('d/m/Y') 
                : $inicio->format('d/m/Y') . ' até ' . $fim->format('d/m/Y'),
            'tendas' => $tendasDisponibilidade,
            'total_tendas_estoque' => $tendasDisponibilidade->sum('quantidade_total'),
            'total_em_locacao' => $tendasDisponibilidade->sum('quantidade_em_locacao'),
            'total_disponivel' => $tendasDisponibilidade->sum('quantidade_disponivel_periodo'),
            'locacoes' => $itensEmLocacao,
            'total_pedidos' => $pedidosAtivos->count(),
        ];
    }
}
