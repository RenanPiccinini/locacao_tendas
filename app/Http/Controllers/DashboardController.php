<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Pedido;
use App\Models\Tenda;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $hoje = Carbon::today();
        $user = auth()->user();

        if ($user->isMontador()) {
            $pedidosMeus = Pedido::where('montador_id', $user->id);
            $totalAtivos = (clone $pedidosMeus)->whereNotIn('status', ['concluido', 'cancelado'])->count();
            $montagensHoje = (clone $pedidosMeus)->whereDate('data_instalacao', $hoje)->count();
            $retiradasHoje = (clone $pedidosMeus)->whereDate('data_retirada', $hoje)->count();
            $pedidosAguardandoRetirada = (clone $pedidosMeus)->where('status', 'aguardando_retirada')->count();

            $proximasInstalacoes = (clone $pedidosMeus)
                ->with(['cliente', 'tendas'])
                ->where('data_instalacao', '>=', $hoje)
                ->whereNotIn('status', ['concluido', 'cancelado'])
                ->orderBy('data_instalacao')
                ->limit(6)
                ->get();

            return view('dashboard', compact(
                'totalAtivos',
                'montagensHoje',
                'retiradasHoje',
                'pedidosAguardandoRetirada',
                'proximasInstalacoes'
            ));
        }

        // Visão do Administrador
        $totalAtivos = Pedido::whereNotIn('status', ['concluido', 'cancelado'])->count();
        $montagensHoje = Pedido::whereDate('data_instalacao', $hoje)->count();
        $retiradasHoje = Pedido::whereDate('data_retirada', $hoje)->count();
        $tendasDisponiveis = Tenda::sum('quantidade_disponivel');
        $totalClientes = Cliente::count();

        $proximasInstalacoes = Pedido::with(['cliente', 'montador', 'tendas'])
            ->where('data_instalacao', '>=', $hoje)
            ->whereNotIn('status', ['concluido', 'cancelado'])
            ->orderBy('data_instalacao')
            ->limit(8)
            ->get();

        return view('dashboard', compact(
            'totalAtivos',
            'montagensHoje',
            'retiradasHoje',
            'tendasDisponiveis',
            'totalClientes',
            'proximasInstalacoes'
        ));
    }
}

