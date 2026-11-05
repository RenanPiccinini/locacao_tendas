<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Pedido;
use App\Models\Tenda;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $hoje = Carbon::today();
        $user = auth()->user();
        $filtro = $request->input('filtro', 'todas'); // 'todas' ou 'hoje'

        if ($user->isMontador()) {
            $pedidosMeus = Pedido::where('montador_id', $user->id);
            $totalAtivos = (clone $pedidosMeus)->whereNotIn('status', ['concluido', 'cancelado'])->count();
            $montagensHoje = (clone $pedidosMeus)->whereDate('data_instalacao', $hoje)->count();
            $retiradasHoje = (clone $pedidosMeus)->whereDate('data_retirada', $hoje)->count();
            $pedidosAguardandoRetirada = (clone $pedidosMeus)->where('status', 'aguardando_retirada')->count();

            $queryMontador = (clone $pedidosMeus)
                ->with(['cliente', 'tendas'])
                ->whereNotIn('status', ['concluido', 'cancelado']);

            // Filtro 'hoje': filtra montagens ou retiradas marcadas para hoje. Filtro 'todas': lista todas mesmo fora do dia (antecipações)
            if ($filtro === 'hoje') {
                $queryMontador->where(function ($q) use ($hoje) {
                    $q->whereDate('data_instalacao', $hoje)
                      ->orWhereDate('data_retirada', $hoje);
                });
            }

            $proximasInstalacoes = $queryMontador
                ->orderBy('data_instalacao')
                ->get();

            return view('dashboard', compact(
                'totalAtivos',
                'montagensHoje',
                'retiradasHoje',
                'pedidosAguardandoRetirada',
                'proximasInstalacoes',
                'filtro'
            ));
        }

        // Visão do Administrador
        $totalAtivos = Pedido::whereNotIn('status', ['concluido', 'cancelado'])->count();
        $montagensHoje = Pedido::whereDate('data_instalacao', $hoje)->count();
        $retiradasHoje = Pedido::whereDate('data_retirada', $hoje)->count();
        $tendasDisponiveis = Tenda::sum('quantidade_disponivel');
        $totalClientes = Cliente::count();

        $queryAdmin = Pedido::with(['cliente', 'montador', 'tendas'])
            ->whereNotIn('status', ['concluido', 'cancelado']);

        if ($filtro === 'hoje') {
            $queryAdmin->where(function ($q) use ($hoje) {
                $q->whereDate('data_instalacao', $hoje)
                  ->orWhereDate('data_retirada', $hoje);
            });
        }

        $proximasInstalacoes = $queryAdmin
            ->orderBy('data_instalacao')
            ->get();

        return view('dashboard', compact(
            'totalAtivos',
            'montagensHoje',
            'retiradasHoje',
            'tendasDisponiveis',
            'totalClientes',
            'proximasInstalacoes',
            'filtro'
        ));
    }
}

