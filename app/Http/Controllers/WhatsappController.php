<?php

namespace App\Http\Controllers;

use App\Services\WhatsappService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WhatsappController extends Controller
{
    protected WhatsappService $whatsapp;

    public function __construct(WhatsappService $whatsapp)
    {
        $this->whatsapp = $whatsapp;
    }

    public function conexao(): View
    {
        $instance = config('services.evolution.instance');
        $apiUrl = config('services.evolution.url');
        return view('whatsapp.conexao', compact('instance', 'apiUrl'));
    }

    public function status(): JsonResponse
    {
        $status = $this->whatsapp->getStatus();
        return response()->json($status);
    }

    public function qrcode(): JsonResponse
    {
        $qrcode = $this->whatsapp->getQrCode();
        return response()->json($qrcode);
    }

    public function desconectar(): JsonResponse
    {
        $sucesso = $this->whatsapp->desconectar();
        return response()->json(['success' => $sucesso]);
    }

    public function testarLembrete(Request $request): JsonResponse
    {
        $tipo = $request->input('tipo', 'dia');
        \Illuminate\Support\Facades\Artisan::call('lembretes:whatsapp', ['--tipo' => $tipo]);
        $output = \Illuminate\Support\Facades\Artisan::output();

        return response()->json([
            'success' => true,
            'tipo' => $tipo,
            'output' => trim($output) ?: 'Comando executado com sucesso!',
        ]);
    }
}

