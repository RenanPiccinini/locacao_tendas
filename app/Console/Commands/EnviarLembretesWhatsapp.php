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
        $inicioSemana = Carbon::now()->startOfWeek();
        $fimSemana = Carbon::now()->endOfWeek();

        $montadores = User::where('role', 'montador')
            ->whereNotNull('whatsapp')
            ->get();

        foreach ($montadores as $montador) {
            $pedidos = Pedido::with(['cliente', 'tendas'])
                ->where('montador_id', $montador->id)
                ->whereBetween('data_instalacao', [$inicioSemana, $fimSemana])
                ->whereNotIn('status', ['concluido', 'cancelado'])
                ->orderBy('data_instalacao')
                ->get();

            if ($pedidos->isEmpty()) {
                continue;
            }

            $msg = "🗓️ *AGENDA DA SEMANA - MONTAGEM DE TENDAS*\n"
                 . "Olá *{$montador->name}*, confira suas montagens agendadas para esta semana ({$inicioSemana->format('d/m')} a {$fimSemana->format('d/m')}):\n\n";

            foreach ($pedidos as $p) {
                $data = $p->data_instalacao->format('d/m (D)');
                $hora = $p->hora_instalacao ? substr($p->hora_instalacao, 0, 5) : 'A combinar';
                $tendas = $p->tendas->map(fn($t) => "{$t->pivot->quantidade}x {$t->nome}")->implode(', ');

                $msg .= "📌 *{$data} às {$hora}*\n"
                      . "• Cliente: {$p->cliente->nome}\n"
                      . "• Local: {$p->endereco_instalacao}\n"
                      . "• Tendas: {$tendas}\n"
                      . "• Status: {$p->status_label}\n\n";
            }

            $msg .= "Acesse o sistema para ver detalhes e atualizar suas Ordens de Serviço!";

            $this->whatsapp->enviarMensagem($montador->whatsapp, $msg);
            $this->info("Lembrete semanal enviado para: {$montador->name}");
        }
    }

    /**
     * Lembrete no dia de cada montagem pela manhã
     */
    protected function enviarLembreteDiario(): void
    {
        $hoje = Carbon::today();

        $pedidos = Pedido::with(['cliente', 'montador', 'tendas'])
            ->whereDate('data_instalacao', $hoje)
            ->whereNotIn('status', ['concluido', 'cancelado'])
            ->get();

        foreach ($pedidos as $pedido) {
            if (!$pedido->montador || empty($pedido->montador->whatsapp)) {
                continue;
            }

            $hora = $pedido->hora_instalacao ? substr($pedido->hora_instalacao, 0, 5) : 'Horário a combinar';
            $tendas = $pedido->tendas->map(fn($t) => "{$t->pivot->quantidade}x {$t->nome} ({$t->tamanho})")->implode(', ');

            $msg = "☀️ *BOM DIA! LEMBRETE DE MONTAGEM HOJE*\n\n"
                 . "Olá *{$pedido->montador->name}*, você tem montagem agendada para hoje:\n\n"
                 . "👤 *Cliente:* {$pedido->cliente->nome}\n"
                 . "📞 *Contato:* {$pedido->cliente->telefone}\n"
                 . "⏰ *Horário:* {$hora}\n"
                 . "📍 *Endereço:* {$pedido->endereco_instalacao}\n"
                 . "⛺ *Tendas:* {$tendas}\n\n"
                 . "Não se esqueça de clicar em *'Em Processo de Instalação'* ao chegar no local!";

            $this->whatsapp->enviarMensagem($pedido->montador->whatsapp, $msg);
            $this->info("Lembrete diário enviado para {$pedido->montador->name} (Pedido #{$pedido->id})");
        }
    }
}

