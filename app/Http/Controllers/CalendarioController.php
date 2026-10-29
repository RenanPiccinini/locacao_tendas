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
            $tendasNomes = $pedido->tendas->pluck('nome')->implode(', ');
            $montadorNome = $pedido->montador ? $pedido->montador->name : 'Sem montador';

            // Evento 1: Instalação
            $eventos[] = [
                'id' => "inst-{$pedido->id}",
                'title' => "⛺ Montagem: {$pedido->cliente->nome} (" . ($user->isAdmin() ? $montadorNome : $tendasNomes) . ")",
                'start' => $pedido->data_instalacao->format('Y-m-d') . ($pedido->hora_instalacao ? 'T' . $pedido->hora_instalacao : ''),
                'allDay' => empty($pedido->hora_instalacao),
                'backgroundColor' => '#2563eb', // Azul
                'borderColor' => '#1d4ed8',
                'url' => route('pedidos.show', $pedido),
                'extendedProps' => [
                    'tipo' => 'Instalação',
                    'cliente' => $pedido->cliente->nome,
                    'endereco' => $pedido->endereco_instalacao,
                    'montador' => $montadorNome,
                    'status' => $pedido->status_label,
                ]
            ];

            // Evento 2: Retirada
            $eventos[] = [
                'id' => "ret-{$pedido->id}",
                'title' => "🔄 Retirada: {$pedido->cliente->nome} (" . ($user->isAdmin() ? $montadorNome : $tendasNomes) . ")",
                'start' => $pedido->data_retirada->format('Y-m-d') . ($pedido->hora_retirada ? 'T' . $pedido->hora_retirada : ''),
                'allDay' => empty($pedido->hora_retirada),
                'backgroundColor' => '#9333ea', // Roxo
                'borderColor' => '#7e22ce',
                'url' => route('pedidos.show', $pedido),
                'extendedProps' => [
                    'tipo' => 'Retirada',
                    'cliente' => $pedido->cliente->nome,
                    'endereco' => $pedido->endereco_instalacao,
                    'montador' => $montadorNome,
                    'status' => $pedido->status_label,
                ]
            ];
        }

        return response()->json($eventos);
    }
}

