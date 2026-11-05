<?php

use App\Http\Controllers\CalendarioController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ContratoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrdemServicoController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TendaController;
use App\Http\Controllers\WhatsappController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Download do Atalho para Computador / Área de Trabalho (Abre direto no Admin do Sistema)
Route::get('atalho-pc', function () {
    $targetUrl = url('/dashboard');
    $logoUrl = asset('images/logo.png');
    $html = <<<HTML
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Tendas Eldorado - Painel Administrativo</title>
    <link rel="icon" type="image/png" href="{$logoUrl}">
    <meta http-equiv="refresh" content="0; url={$targetUrl}">
    <script>
        window.location.replace("{$targetUrl}");
    </script>
</head>
<body style="margin:0;background:#02140e;color:#10b981;font-family:Arial,Helvetica,sans-serif;display:flex;align-items:center;justify-content:center;height:100vh;">
    <div style="text-align:center;padding:20px;">
        <h2 style="color:#fbbf24;margin-bottom:8px;font-size:22px;">Tendas Eldorado</h2>
        <p style="font-size:14px;color:#d1fae5;">Abrindo Painel Administrativo do Sistema...</p>
    </div>
</body>
</html>
HTML;

    return response($html, 200, [
        'Content-Type' => 'text/html; charset=utf-8',
        'Content-Disposition' => 'attachment; filename="Tendas Eldorado - Painel.html"',
    ]);
})->name('atalho.pc');

Route::get('atalho-url', function () {
    $targetUrl = url('/dashboard');
    $content = "[InternetShortcut]\r\nURL={$targetUrl}\r\nIconIndex=0\r\nIconFile=" . asset('images/logo.png') . "\r\n";
    return response($content, 200, [
        'Content-Type' => 'application/x-mswinurl; charset=utf-8',
        'Content-Disposition' => 'attachment; filename="Tendas Eldorado - Painel.url"',
    ]);
})->name('atalho.url');

Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Pedidos (Montador e Admin acessam com escopos adequados)
    Route::get('pedidos/disponibilidade-tendas', [PedidoController::class, 'consultarDisponibilidadeTendas'])->name('pedidos.disponibilidade-tendas');
    Route::resource('pedidos', PedidoController::class);
    Route::post('pedidos/{pedido}/gerar-os', [PedidoController::class, 'gerarOs'])->name('pedidos.gerar-os');
    Route::post('pedidos/{pedido}/iniciar-instalacao', [PedidoController::class, 'iniciarInstalacao'])->name('pedidos.iniciar-instalacao');
    Route::post('pedidos/{pedido}/anexar-fotos', [PedidoController::class, 'anexarFotos'])->name('pedidos.anexar-fotos');
    Route::post('pedidos/{pedido}/finalizar-instalacao', [PedidoController::class, 'finalizarInstalacao'])->name('pedidos.finalizar-instalacao');
    Route::post('pedidos/{pedido}/iniciar-retirada', [PedidoController::class, 'iniciarRetirada'])->name('pedidos.iniciar-retirada');
    Route::post('pedidos/{pedido}/confirmar-retirada', [PedidoController::class, 'confirmarRetirada'])->name('pedidos.confirmar-retirada');

    // Ordens de Serviço (Visualização e Download em PDF, e Reenvio WhatsApp)
    Route::get('ordens-servico/{ordemServico}/pdf', [OrdemServicoController::class, 'pdf'])->name('os.pdf');
    Route::get('ordens-servico/{ordemServico}/download', [OrdemServicoController::class, 'download'])->name('os.download');
    Route::post('ordens-servico/{ordemServico}/enviar-whatsapp', [OrdemServicoController::class, 'enviarWhatsapp'])->name('os.enviar-whatsapp');

    // Contratos de Locação por Pedido (Visualização, Download e Emissão)
    Route::get('pedidos/{pedido}/contrato', [ContratoController::class, 'verPedido'])->name('pedidos.contrato.ver');
    Route::get('pedidos/{pedido}/contrato/download', [ContratoController::class, 'downloadPedido'])->name('pedidos.contrato.download');
    Route::post('pedidos/{pedido}/gerar-contrato', [ContratoController::class, 'gerarPedido'])->name('pedidos.contrato.gerar');

    // Consulta de CEP (Integração com ViaCEP com proxy e fallback de rede)
    Route::get('consulta-cep/{cep}', function (string $cep) {
        $limpo = preg_replace('/\D/', '', $cep);
        if (strlen($limpo) !== 8) {
            return response()->json(['erro' => true, 'mensagem' => 'CEP inválido. Deve conter 8 dígitos.'], 422);
        }

        try {
            $res = \Illuminate\Support\Facades\Http::timeout(5)->get("https://viacep.com.br/ws/{$limpo}/json/");
            if ($res->successful()) {
                return $res->json();
            }
        } catch (\Throwable $e) {
            // Fallback silencioso
        }

        return response()->json(['erro' => true, 'mensagem' => 'Não foi possível consultar o CEP.'], 500);
    })->name('cep.consulta');

    // Calendário (Montador vê os dele, Admin vê todos)
    Route::get('calendario', [CalendarioController::class, 'index'])->name('calendario.index');
    Route::get('calendario/eventos', [CalendarioController::class, 'eventos'])->name('calendario.eventos');

    // Estoque de Tendas (Visualização de estoque e disponibilidade liberada para Montadores e Admins)
    Route::get('tendas', [TendaController::class, 'index'])->name('tendas.index');

    // Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rotas exclusivas para Administradores
    Route::middleware('admin')->group(function () {
        // Gestão de Usuários do Sistema (Somente Admins)
        Route::resource('usuarios', \App\Http\Controllers\UsuarioController::class)->except(['show']);
        Route::post('usuarios/{usuario}/toggle-bloqueio', [\App\Http\Controllers\UsuarioController::class, 'toggleBloqueio'])->name('usuarios.toggle-bloqueio');
        Route::post('usuarios/{usuario}/alterar-senha', [\App\Http\Controllers\UsuarioController::class, 'alterarSenha'])->name('usuarios.alterar-senha');

        Route::resource('montadores', \App\Http\Controllers\MontadorController::class);
        Route::resource('tendas', TendaController::class)->except(['index']);
        Route::resource('clientes', ClienteController::class);
        Route::post('clientes/{cliente}/gerar-contrato', [ContratoController::class, 'gerar'])->name('clientes.contrato.gerar');
        Route::get('clientes/{cliente}/contrato', [ContratoController::class, 'ver'])->name('clientes.contrato.ver');
        Route::get('clientes/{cliente}/contrato/download', [ContratoController::class, 'download'])->name('clientes.contrato.download');

        // WhatsApp Conexão (Evolution API)
        Route::get('whatsapp/conexao', [WhatsappController::class, 'conexao'])->name('whatsapp.conexao');
        Route::get('whatsapp/status', [WhatsappController::class, 'status'])->name('whatsapp.status');
        Route::get('whatsapp/qrcode', [WhatsappController::class, 'qrcode'])->name('whatsapp.qrcode');
        Route::post('whatsapp/desconectar', [WhatsappController::class, 'desconectar'])->name('whatsapp.desconectar');
        Route::post('whatsapp/testar-lembrete', [WhatsappController::class, 'testarLembrete'])->name('whatsapp.testar-lembrete');
    });
});

// Endpoints Web Cron (para Hostinger ou chamadas agendadas via curl/HTTP)
Route::get('cron/scheduler', function () {
    \Illuminate\Support\Facades\Artisan::call('schedule:run');
    return response()->json([
        'success' => true,
        'message' => 'Scheduler do Laravel executado com sucesso!',
        'output' => trim(\Illuminate\Support\Facades\Artisan::output()) ?: 'Nenhuma tarefa pendente para o minuto atual.',
        'timestamp' => now('America/Sao_Paulo')->toIso8601String(),
    ]);
})->name('cron.scheduler');

Route::get('cron/lembretes-whatsapp', function (\Illuminate\Http\Request $request) {
    $tipo = $request->query('tipo', 'dia');
    \Illuminate\Support\Facades\Artisan::call('lembretes:whatsapp', ['--tipo' => $tipo]);
    return response()->json([
        'success' => true,
        'tipo' => $tipo,
        'message' => "Lembrete ({$tipo}) disparado via Web Cron!",
        'output' => trim(\Illuminate\Support\Facades\Artisan::output()),
        'timestamp' => now('America/Sao_Paulo')->toIso8601String(),
    ]);
})->name('cron.lembretes');

// Endpoint para criação ou verificação do link simbólico de storage na Hostinger via Web
Route::get('cron/storage-link', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('storage:link');
        return response()->json([
            'success' => true,
            'message' => 'Symlink do storage criado/verificado com sucesso!',
            'output' => trim(\Illuminate\Support\Facades\Artisan::output()),
            'timestamp' => now('America/Sao_Paulo')->toIso8601String(),
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
        ], 500);
    }
})->name('cron.storage-link');

// Endpoint para executar migrações de banco na Hostinger via Web se necessário
Route::get('cron/migrate', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        return response()->json([
            'success' => true,
            'message' => 'Migrações do banco de dados executadas com sucesso!',
            'output' => trim(\Illuminate\Support\Facades\Artisan::output()),
            'timestamp' => now('America/Sao_Paulo')->toIso8601String(),
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
        ], 500);
    }
})->name('cron.migrate');

// Rotas de entrega direta de arquivos do storage (fotos de instalação, contratos e anexos)
// Garante funcionamento imediato na Hostinger sem depender de symlink ou permissões de servidor
Route::get('storage-file/{path}', [PedidoController::class, 'servirArquivoStorage'])
    ->where('path', '.*')
    ->name('storage.file');

Route::get('storage/{path}', [PedidoController::class, 'servirArquivoStorage'])
    ->where('path', '.*');

require __DIR__.'/auth.php';
