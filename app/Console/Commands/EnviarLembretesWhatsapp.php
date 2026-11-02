<?php

namespace App\Console\Commands;

use App\Models\Pedido;
use App\Models\User;
use App\Services\WhatsappService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class EnviarLembretesWhatsapp extends Command
{
    protected $signature = 'lembretes:whatsapp {--tipo=dia : Tipo de lembrete: "semana" (segunda-feira) ou "dia" (do dia)}';
    protected $description = 'Envia lembretes automáticos para os montadores via WhatsApp';

    protected WhatsappService $whatsapp;

    public function __construct(WhatsappService $whatsapp)
    {
        parent::__construct();
        $this->whatsapp = $whatsapp;
    }

    public function handle()
    {
        $tipo = $this->option('tipo');

        if ($tipo === 'semana') {
            $this->enviarLembreteSemanal();
        } else {
            $this->enviarLembreteDiario();
        }

        return Command::SUCCESS;
    }

    /**
     * Lembrete de toda segunda-feira com as montagens da semana para cada montador
     */
    protected function enviarLembreteSemanal(): void
    {
        $hoje = Carbon::now('America/Sao_Paulo');
        $inicioSemana = $hoje->copy()->startOfWeek();
        $fimSemana = $hoje->copy()->endOfWeek();

        $montadores = User::where('role', 'montador')
            ->whereNotNull('whatsapp')
            ->get();

        $enviados = 0;

        foreach ($montadores as $montador) {
            $pedidosInstalacao = Pedido::with(['cliente', 'tendas'])
                ->where('montador_id', $montador->id)
                ->whereDate('data_instalacao', '>=', $inicioSemana->format('Y-m-d'))
                ->whereDate('data_instalacao', '<=', $fimSemana->format('Y-m-d'))
                ->whereNotIn('status', ['concluido', 'cancelado'])
                ->orderBy('data_instalacao')
                ->orderBy('hora_instalacao')
                ->get();

            $pedidosRetirada = Pedido::with(['cliente', 'tendas'])
                ->where('montador_id', $montador->id)
                ->whereDate('data_retirada', '>=', $inicioSemana->format('Y-m-d'))
                ->whereDate('data_retirada', '<=', $fimSemana->format('Y-m-d'))
                ->whereIn('status', ['instalado', 'aguardando_retirada', 'em_retirada'])
                ->orderBy('data_retirada')
                ->orderBy('hora_retirada')
                ->get();

            if ($pedidosInstalacao->isEmpty() && $pedidosRetirada->isEmpty()) {
                continue;
            }

            $msg = "🗓️ *AGENDA DA SEMANA - LOCAÇÃO DE TENDAS*\n"
                 . "Olá *{$montador->name}*, confira sua programação para esta semana ({$inicioSemana->format('d/m')} a {$fimSemana->format('d/m')}):\n\n";

            if ($pedidosInstalacao->isNotEmpty()) {
                $msg .= "⛺ *MONTAGENS / INSTALAÇÕES:*\n";
                foreach ($pedidosInstalacao as $p) {
                    $data = $p->data_instalacao->format('d/m (D)');
                    $hora = $p->hora_instalacao ? substr($p->hora_instalacao, 0, 5) : 'A combinar';
                    $tendas = $p->tendas->map(fn($t) => "{$t->pivot->quantidade}x {$t->nome}")->implode(', ');

                    $msg .= "• *{$data} às {$hora}* (Pedido #{$p->id})\n"
                          . "  Cliente: {$p->cliente->nome} ({$p->cliente->telefone})\n"
                          . "  Local: {$p->endereco_instalacao}" . ($p->cidade_instalacao ? " - {$p->cidade_instalacao}" : "") . "\n"
                          . "  Tendas: {$tendas}\n"
                          . "  Status: {$p->status_label}\n\n";
                }
            }

            if ($pedidosRetirada->isNotEmpty()) {
                $msg .= "🔄 *RETIRADAS / DESMONTAGENS:*\n";
                foreach ($pedidosRetirada as $p) {
                    $data = $p->data_retirada->format('d/m (D)');
                    $hora = $p->hora_retirada ? substr($p->hora_retirada, 0, 5) : 'A combinar';
                    $tendas = $p->tendas->map(fn($t) => "{$t->pivot->quantidade}x {$t->nome}")->implode(', ');

                    $msg .= "• *{$data} às {$hora}* (Pedido #{$p->id})\n"
                          . "  Cliente: {$p->cliente->nome}\n"
                          . "  Local: {$p->endereco_instalacao}\n"
                          . "  Tendas a recolher: {$tendas}\n\n";
                }
            }

            $msg .= "📲 Acesse o sistema pelo celular para emitir e atualizar suas Ordens de Serviço!";

            $this->whatsapp->enviarMensagem($montador->whatsapp, $msg);
            $this->info("Lembrete semanal enviado para: {$montador->name}");
            $enviados++;
        }

        if ($enviados === 0) {
            $this->info("Nenhum montador com serviços agendados para esta semana.");
        }
    }

    /**
     * Lembrete no dia de cada montagem e retirada pela manhã
     */
    protected function enviarLembreteDiario(): void
    {
        $hojeStr = Carbon::now('America/Sao_Paulo')->format('Y-m-d');
        $enviados = 0;

        // 1. Montagens do dia
        $pedidosInstalacao = Pedido::with(['cliente', 'montador', 'tendas'])
            ->whereDate('data_instalacao', $hojeStr)
            ->whereNotIn('status', ['instalado', 'aguardando_retirada', 'em_retirada', 'concluido', 'cancelado'])
            ->get();

        foreach ($pedidosInstalacao as $pedido) {
            if (!$pedido->montador || empty($pedido->montador->whatsapp)) {
                continue;
            }

            $hora = $pedido->hora_instalacao ? substr($pedido->hora_instalacao, 0, 5) : 'Horário a combinar';
            $tendas = $pedido->tendas->map(fn($t) => "{$t->pivot->quantidade}x {$t->nome} ({$t->tamanho})")->implode(', ');

            $msg = "☀️ *BOM DIA! LEMBRETE DE MONTAGEM HOJE*\n\n"
                 . "Olá *{$pedido->montador->name}*, você tem montagem agendada para hoje:\n\n"
                 . "📋 *Pedido:* #{$pedido->id}\n"
                 . "👤 *Cliente:* {$pedido->cliente->nome}\n"
                 . "📞 *Contato:* {$pedido->cliente->telefone}\n"
                 . "⏰ *Horário:* {$hora}\n"
                 . "📍 *Endereço:* {$pedido->endereco_instalacao}" . ($pedido->cidade_instalacao ? " - {$pedido->cidade_instalacao}" : "") . "\n"
                 . "⛺ *Tendas:* {$tendas}\n"
                 . "📝 *Instruções:* " . ($pedido->observacoes ?: 'Sem observações adicionais') . "\n\n"
                 . "📲 Ao chegar no local, acesse o sistema no seu celular e clique em *'Estou em Processo de Instalação'*!";

            $this->whatsapp->enviarMensagem($pedido->montador->whatsapp, $msg);
            $this->info("Lembrete de montagem enviado para {$pedido->montador->name} (Pedido #{$pedido->id})");
            $enviados++;
        }

        // 2. Retiradas do dia
        $pedidosRetirada = Pedido::with(['cliente', 'montador', 'tendas'])
            ->whereDate('data_retirada', $hojeStr)
            ->whereIn('status', ['instalado', 'aguardando_retirada', 'em_retirada'])
            ->get();

        foreach ($pedidosRetirada as $pedido) {
            if (!$pedido->montador || empty($pedido->montador->whatsapp)) {
                continue;
            }

            $hora = $pedido->hora_retirada ? substr($pedido->hora_retirada, 0, 5) : 'Horário a combinar';
            $tendas = $pedido->tendas->map(fn($t) => "{$t->pivot->quantidade}x {$t->nome} ({$t->tamanho})")->implode(', ');

            $msg = "🔄 *LEMBRETE DE RETIRADA HOJE!*\n\n"
                 . "Olá *{$pedido->montador->name}*, você tem retirada de tendas agendada para hoje:\n\n"
                 . "📋 *Pedido:* #{$pedido->id}\n"
                 . "👤 *Cliente:* {$pedido->cliente->nome}\n"
                 . "📞 *Contato:* {$pedido->cliente->telefone}\n"
                 . "⏰ *Horário Previsto:* {$hora}\n"
                 . "📍 *Local de Retirada:* {$pedido->endereco_instalacao}" . ($pedido->cidade_instalacao ? " - {$pedido->cidade_instalacao}" : "") . "\n"
                 . "⛺ *Tendas a recolher:* {$tendas}\n\n"
                 . "📲 Após desmontar e recolher, clique em *'Confirmar Retirada'* no sistema para retornar os itens ao estoque!";

            $this->whatsapp->enviarMensagem($pedido->montador->whatsapp, $msg);
            $this->info("Lembrete de retirada enviado para {$pedido->montador->name} (Pedido #{$pedido->id})");
            $enviados++;
        }

        if ($enviados === 0) {
            $this->info("Nenhum serviço agendado para hoje.");
        }
    }
}
