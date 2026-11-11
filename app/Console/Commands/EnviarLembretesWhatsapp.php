<?php

namespace App\Console\Commands;

use App\Models\Pedido;
use App\Models\User;
use App\Services\WhatsappService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

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
            ->where('whatsapp', '!=', '')
            ->get();

        $enviados = 0;

        foreach ($montadores as $montador) {
            $lockKey = "lembrete_whatsapp_semana_{$montador->id}_" . $inicioSemana->format('Y_W');
            if (Cache::has($lockKey)) {
                $this->info("Lembrete semanal já enviado para {$montador->name} nesta semana ({$inicioSemana->format('d/m')}). Evitando repetição.");
                continue;
            }

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

            $msg = "🗓️ *AGENDA DA SEMANA - TENDAS ELDORADO*\n"
                 . "Olá *{$montador->name}*, confira sua programação para esta semana ({$inicioSemana->format('d/m')} a {$fimSemana->format('d/m')}):\n\n";

            if ($pedidosInstalacao->isNotEmpty()) {
                $msg .= "⛺ *MONTAGENS / INSTALAÇÕES DA SEMANA:*\n";
                foreach ($pedidosInstalacao as $p) {
                    $data = $p->data_instalacao->format('d/m (D)');
                    $hora = $p->hora_instalacao ? substr($p->hora_instalacao, 0, 5) : 'Horário livre';
                    $tendas = $p->tendas->map(fn($t) => "{$t->pivot->quantidade}x {$t->nome}")->implode(', ');

                    $resp = '';
                    if ($p->nome_responsavel) {
                        $resp = "  Contato: {$p->nome_responsavel}" . ($p->telefone_responsavel ? " ({$p->telefone_responsavel})" : "") . "\n";
                    }

                    $msg .= "• *{$data} às {$hora}* (Pedido #{$p->id})\n"
                          . "  Cliente: {$p->cliente->nome} ({$p->cliente->telefone})\n"
                          . $resp
                          . "  Local: {$p->endereco_instalacao}" . ($p->cidade_instalacao ? " - {$p->cidade_instalacao}" : "") . "\n"
                          . "  Tendas: {$tendas}\n\n";
                }
            }

            if ($pedidosRetirada->isNotEmpty()) {
                $msg .= "🔄 *RETIRADAS DA SEMANA:*\n";
                foreach ($pedidosRetirada as $p) {
                    $data = $p->data_retirada->format('d/m (D)');
                    $hora = $p->hora_retirada ? substr($p->hora_retirada, 0, 5) : 'Horário livre';
                    $tendas = $p->tendas->map(fn($t) => "{$t->pivot->quantidade}x {$t->nome}")->implode(', ');

                    $msg .= "• *{$data} às {$hora}* (Pedido #{$p->id})\n"
                          . "  Cliente: {$p->cliente->nome}\n"
                          . "  Local: {$p->endereco_instalacao}" . ($p->cidade_instalacao ? " - {$p->cidade_instalacao}" : "") . "\n"
                          . "  Tendas a recolher: {$tendas}\n\n";
                }
            }

            $msg .= "📲 Acesse o sistema pelo celular para registrar *'Tenda instalada'* e *'Tenda retirada'*!";

            $this->whatsapp->enviarMensagem($montador->whatsapp, $msg);
            Cache::put($lockKey, true, now()->addDays(7));
            $this->info("Lembrete semanal enviado para: {$montador->name}");
            $enviados++;
        }

        if ($enviados === 0) {
            $this->info("Nenhum montador com serviços agendados para esta semana.");
        }
    }

    /**
     * Lembrete diário às 08h00 com as montagens e retiradas de hoje para cada montador
     */
    protected function enviarLembreteDiario(): void
    {
        $hoje = Carbon::now('America/Sao_Paulo');
        $hojeStr = $hoje->format('Y-m-d');
        $enviados = 0;

        $montadores = User::where('role', 'montador')
            ->whereNotNull('whatsapp')
            ->where('whatsapp', '!=', '')
            ->get();

        foreach ($montadores as $montador) {
            $lockKey = "lembrete_whatsapp_dia_{$montador->id}_{$hojeStr}";
            if (Cache::has($lockKey)) {
                $this->info("Lembrete diário já enviado para {$montador->name} hoje ({$hojeStr}). Evitando repetição.");
                continue;
            }

            $pedidosInstalacao = Pedido::with(['cliente', 'tendas'])
                ->where('montador_id', $montador->id)
                ->whereDate('data_instalacao', $hojeStr)
                ->whereNotIn('status', ['instalado', 'aguardando_retirada', 'em_retirada', 'concluido', 'cancelado'])
                ->orderBy('hora_instalacao')
                ->get();

            $pedidosRetirada = Pedido::with(['cliente', 'tendas'])
                ->where('montador_id', $montador->id)
                ->whereDate('data_retirada', $hojeStr)
                ->whereIn('status', ['instalado', 'aguardando_retirada', 'em_retirada'])
                ->orderBy('hora_retirada')
                ->get();

            if ($pedidosInstalacao->isEmpty() && $pedidosRetirada->isEmpty()) {
                continue;
            }

            $msg = "☀️ *BOM DIA! AGENDA DE HOJE ({$hoje->format('d/m/Y')}) - TENDAS ELDORADO*\n"
                 . "Olá *{$montador->name}*, confira seus atendimentos agendados para hoje:\n\n";

            if ($pedidosInstalacao->isNotEmpty()) {
                $msg .= "⛺ *MONTAGENS / INSTALAÇÕES DE HOJE:*\n";
                foreach ($pedidosInstalacao as $p) {
                    $hora = $p->hora_instalacao ? substr($p->hora_instalacao, 0, 5) : 'Horário livre';
                    $tendas = $p->tendas->map(fn($t) => "{$t->pivot->quantidade}x {$t->nome} ({$t->tamanho})")->implode(', ');

                    $resp = '';
                    if ($p->nome_responsavel) {
                        $resp = "  Contato no Local: {$p->nome_responsavel}" . ($p->telefone_responsavel ? " ({$p->telefone_responsavel})" : "") . "\n";
                    }

                    $msg .= "• *Pedido #{$p->id}* às *{$hora}*\n"
                          . "  Cliente: {$p->cliente->nome} ({$p->cliente->telefone})\n"
                          . $resp
                          . "  Local: {$p->endereco_instalacao}" . ($p->cidade_instalacao ? " - {$p->cidade_instalacao}" : "") . "\n"
                          . "  Tendas: {$tendas}\n"
                          . ($p->observacoes ? "  Obs: {$p->observacoes}\n" : "")
                          . "\n";
                }
            }

            if ($pedidosRetirada->isNotEmpty()) {
                $msg .= "🔄 *RETIRADAS DE HOJE:*\n";
                foreach ($pedidosRetirada as $p) {
                    $hora = $p->hora_retirada ? substr($p->hora_retirada, 0, 5) : 'Horário livre';
                    $tendas = $p->tendas->map(fn($t) => "{$t->pivot->quantidade}x {$t->nome} ({$t->tamanho})")->implode(', ');

                    $msg .= "• *Pedido #{$p->id}* às *{$hora}*\n"
                          . "  Cliente: {$p->cliente->nome} ({$p->cliente->telefone})\n"
                          . "  Local: {$p->endereco_instalacao}" . ($p->cidade_instalacao ? " - {$p->cidade_instalacao}" : "") . "\n"
                          . "  Tendas a recolher: {$tendas}\n\n";
                }
            }

            $msg .= "📲 Ao concluir cada serviço, acesse o sistema no celular para registrar *'Tenda instalada'* ou *'Tenda retirada'*!";

            $this->whatsapp->enviarMensagem($montador->whatsapp, $msg);
            Cache::put($lockKey, true, now()->addDays(1));
            $this->info("Lembrete diário enviado para {$montador->name}");
            $enviados++;
        }

        if ($enviados === 0) {
            $this->info("Nenhum serviço agendado para hoje.");
        }
    }
}
