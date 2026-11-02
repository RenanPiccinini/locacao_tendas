<?php

use App\Http\Controllers\CalendarioController;
use App\Http\Controllers\ClienteController;
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

Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Pedidos (Montador e Admin acessam com escopos adequados)
    Route::resource('pedidos', PedidoController::class);
    Route::post('pedidos/{pedido}/gerar-os', [PedidoController::class, 'gerarOs'])->name('pedidos.gerar-os');
    Route::post('pedidos/{pedido}/iniciar-instalacao', [PedidoController::class, 'iniciarInstalacao'])->name('pedidos.iniciar-instalacao');
    Route::post('pedidos/{pedido}/anexar-fotos', [PedidoController::class, 'anexarFotos'])->name('pedidos.anexar-fotos');
    Route::post('pedidos/{pedido}/finalizar-instalacao', [PedidoController::class, 'finalizarInstalacao'])->name('pedidos.finalizar-instalacao');
    Route::post('pedidos/{pedido}/iniciar-retirada', [PedidoController::class, 'iniciarRetirada'])->name('pedidos.iniciar-retirada');
    Route::post('pedidos/{pedido}/confirmar-retirada', [PedidoController::class, 'confirmarRetirada'])->name('pedidos.confirmar-retirada');

    // Ordens de Serviço (Visualização em PDF)
    Route::get('ordens-servico/{ordemServico}/pdf', [OrdemServicoController::class, 'pdf'])->name('os.pdf');

    // Calendário (Montador vê os dele, Admin vê todos)
    Route::get('calendario', [CalendarioController::class, 'index'])->name('calendario.index');
    Route::get('calendario/eventos', [CalendarioController::class, 'eventos'])->name('calendario.eventos');

    // Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rotas exclusivas para Administradores
    Route::middleware('admin')->group(function () {
        Route::resource('montadores', \App\Http\Controllers\MontadorController::class);
        Route::resource('tendas', TendaController::class);
        Route::resource('clientes', ClienteController::class);

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

require __DIR__.'/auth.php';
