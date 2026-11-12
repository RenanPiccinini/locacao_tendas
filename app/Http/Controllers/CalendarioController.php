<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CalendarioController extends Controller
{
    public function index(): View
    {
        return view('calendario.index');
    }

    public function eventos(Request $request): JsonResponse
    {
        $user = auth()->user();

        $query = Pedido::with(['cliente', 'montador', 'tendas']);

        // Se for montador, vê apenas as instalações/retiradas dele!
        // Admin vê de todos!
        if ($user->isMontador()) {
            $query->where('montador_id', $user->id);
        }

        if ($request->filled('start') && $request->filled('end')) {
            $start = substr($request->start, 0, 10);
            $end = substr($request->end, 0, 10);
            $query->where(function ($q) use ($start, $end) {
                $q->whereBetween('data_instalacao', [$start, $end])
                  ->orWhereBetween('data_retirada', [$start, $end]);
            });
        }

        $pedidos = $query->get();
        $eventos = [];

        foreach ($pedidos as $pedido) {
            $montadorNome = $pedido->montador ? $pedido->montador->name : 'Sem montador';
            $clienteNome = $pedido->cliente ? $pedido->cliente->nome : 'Cliente não informado';
            $endereco = $pedido->endereco_instalacao . ($pedido->cidade_instalacao ? " - {$pedido->cidade_instalacao}" : "");
            $urlPedido = route('pedidos.show', $pedido);

            // Evento 1: Instalação
            $eventos[] = [
                'id' => "inst-{$pedido->id}",
                'title' => "#{$pedido->id}",
                'start' => $pedido->data_instalacao->format('Y-m-d') . ($pedido->hora_instalacao ? 'T' . $pedido->hora_instalacao : ''),
                'allDay' => empty($pedido->hora_instalacao),
                'backgroundColor' => '#059669', // Verde Esmeralda
                'borderColor' => '#047857',
                'url' => $urlPedido,
                'extendedProps' => [
                    'pedido_id' => $pedido->id,
                    'pedido_url' => $urlPedido,
                    'tipo' => 'instalacao',
                    'tipo_label' => 'Montagem / Instalação',
                    'cliente' => $clienteNome,
                    'cliente_nome' => $clienteNome,
                    'cliente_telefone' => $pedido->cliente?->telefone ?? '',
                    'endereco' => $endereco,
                    'montador' => $montadorNome,
                    'montador_nome' => $montadorNome,
                    'status' => $pedido->status_label,
                    'status_label' => $pedido->status_label,
                ]
            ];

            // Evento 2: Retirada
            $eventos[] = [
                'id' => "ret-{$pedido->id}",
                'title' => "#{$pedido->id}",
                'start' => $pedido->data_retirada->format('Y-m-d') . ($pedido->hora_retirada ? 'T' . $pedido->hora_retirada : ''),
                'allDay' => empty($pedido->hora_retirada),
                'backgroundColor' => '#9333ea', // Roxo
                'borderColor' => '#7e22ce',
                'url' => $urlPedido,
                'extendedProps' => [
                    'pedido_id' => $pedido->id,
                    'pedido_url' => $urlPedido,
                    'tipo' => 'retirada',
                    'tipo_label' => 'Retirada / Devolução',
                    'cliente' => $clienteNome,
                    'cliente_nome' => $clienteNome,
                    'cliente_telefone' => $pedido->cliente?->telefone ?? '',
                    'endereco' => $endereco,
                    'montador' => $montadorNome,
                    'montador_nome' => $montadorNome,
                    'status' => $pedido->status_label,
                    'status_label' => $pedido->status_label,
                ]
            ];
        }

        return response()->json($eventos);
    }
}

