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
    protected $signature = 'lembretes:whatsapp
                            {--tipo=dia : Tipo de lembrete: "semana" (segunda-feira) ou "dia" (do dia)}
                            {--forcar : Ignora a trava de segunda-feira no lembrete diário (para testes manuais)}';
    protected $description = 'Envia lembretes automáticos das instalações para os montadores via WhatsApp';

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
            // NOTIFICAÇÃO DIÁRIA DESATIVADA TEMPORARIAMENTE:
            // O sistema mantém apenas a notificação semanal toda segunda-feira.
            // Para reativar o lembrete diário no futuro, basta descomentar a linha abaixo:
            // $this->enviarLembreteDiario();
            $this->info('Notificação diária desativada temporariamente. Utilizando somente o lembrete semanal de segunda-feira.');
        }

        return Command::SUCCESS;
    }

    /**
     * Lembrete de toda segunda-feira às 07h00 com todas as instalações da semana para cada montador
     */
    protected function enviarLembreteSemanal(): void
    {
        $hoje = Carbon::now('America/Sao_Paulo');
        $inicioSemana = $hoje->copy()->startOfWeek(Carbon::MONDAY)->startOfDay();
        $fimSemana = $hoje->copy()->endOfWeek(Carbon::SUNDAY)->endOfDay();

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
                ->whereNotIn('status', ['instalado', 'aguardando_retirada', 'em_retirada', 'concluido', 'cancelado'])
                ->orderBy('data_instalacao')
                ->orderBy('hora_instalacao')
                ->get();

            $pedidosDesmontagem = Pedido::with(['cliente', 'tendas'])
                ->where('montador_id', $montador->id)
                ->whereDate('data_retirada', '>=', $inicioSemana->format('Y-m-d'))
                ->whereDate('data_retirada', '<=', $fimSemana->format('Y-m-d'))
                ->whereNotIn('status', ['concluido', 'cancelado'])
                ->orderBy('data_retirada')
                ->orderBy('hora_retirada')
                ->get();

            if ($pedidosInstalacao->isEmpty() && $pedidosDesmontagem->isEmpty()) {
                continue;
            }

            $msg = "🗓️ *AGENDA DA SEMANA - TENDAS ELDORADO*\n";
            if ($pedidosInstalacao->isNotEmpty() && $pedidosDesmontagem->isNotEmpty()) {
                $msg .= "Olá *{$montador->name}*, confira suas instalações e desmontagens programadas para esta semana ({$inicioSemana->format('d/m')} a {$fimSemana->format('d/m')}):\n\n";
            } elseif ($pedidosDesmontagem->isNotEmpty()) {
                $msg .= "Olá *{$montador->name}*, confira suas desmontagens programadas para esta semana ({$inicioSemana->format('d/m')} a {$fimSemana->format('d/m')}):\n\n";
            } else {
                $msg .= "Olá *{$montador->name}*, confira suas instalações programadas para esta semana ({$inicioSemana->format('d/m')} a {$fimSemana->format('d/m')}):\n\n";
            }

            if ($pedidosInstalacao->isNotEmpty()) {
                $msg .= "⛺ *INSTALAÇÕES DA SEMANA:*\n";
                foreach ($pedidosInstalacao as $p) {
                    $data = Carbon::parse($p->data_instalacao)->locale('pt_BR')->translatedFormat('d/m (D)');
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

            if ($pedidosDesmontagem->isNotEmpty()) {
                $msg .= "🔄 *DESMONTAGENS DA SEMANA:*\n";
                foreach ($pedidosDesmontagem as $p) {
                    $dataRet = Carbon::parse($p->data_retirada)->locale('pt_BR')->translatedFormat('d/m (D)');
                    $horaRet = $p->hora_retirada ? substr($p->hora_retirada, 0, 5) : 'Horário livre';
                    $tendasRet = $p->tendas->map(fn($t) => "{$t->pivot->quantidade}x {$t->nome}")->implode(', ');

                    $respRet = '';
                    if ($p->nome_responsavel) {
                        $respRet = "  Contato: {$p->nome_responsavel}" . ($p->telefone_responsavel ? " ({$p->telefone_responsavel})" : "") . "\n";
                    }

                    $msg .= "• *{$dataRet} às {$horaRet}* (Pedido #{$p->id})\n"
                          . "  Cliente: {$p->cliente->nome} ({$p->cliente->telefone})\n"
                          . $respRet
                          . "  Local: {$p->endereco_instalacao}" . ($p->cidade_instalacao ? " - {$p->cidade_instalacao}" : "") . "\n"
                          . "  Tendas: {$tendasRet}\n\n";
                }
            }

            if ($pedidosInstalacao->isNotEmpty() && $pedidosDesmontagem->isNotEmpty()) {
                $msg .= "📲 Acesse o sistema pelo celular para registrar *'Tenda instalada'* e *'Tenda retirada'*!";
            } elseif ($pedidosDesmontagem->isNotEmpty()) {
                $msg .= "📲 Acesse o sistema pelo celular para registrar *'Tenda retirada'*!";
            } else {
                $msg .= "📲 Acesse o sistema pelo celular para registrar *'Tenda instalada'*!";
            }

            if ($this->whatsapp->enviarMensagem($montador->whatsapp, $msg)) {
                Cache::put($lockKey, true, $fimSemana);
                $this->info("Lembrete semanal enviado para: {$montador->name}");
                $enviados++;
            } else {
                Log::warning("Falha ao enviar lembrete semanal para {$montador->name} ({$montador->whatsapp}).");
            }
        }

        if ($enviados === 0) {
            $this->info("Nenhum montador com instalações ou desmontagens agendadas para esta semana.");
        }
    }

    /**
     * Lembrete diário às 07h00 (terça a domingo) com somente as instalações de hoje para cada montador
     */
    protected function enviarLembreteDiario(): void
    {
        $hoje = Carbon::now('America/Sao_Paulo');
        if ($hoje->isMonday() && !$this->option('forcar')) {
            $this->info('Hoje é segunda-feira: o lembrete semanal já envia todas as instalações da semana. Pulando lembrete diário.');
            return;
        }

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

            if ($pedidosInstalacao->isEmpty()) {
                continue;
            }

            $msg = "☀️ *BOM DIA! INSTALAÇÕES DE HOJE ({$hoje->format('d/m/Y')}) - TENDAS ELDORADO*\n"
                 . "Olá *{$montador->name}*, confira suas instalações agendadas para hoje:\n\n";

            $msg .= "⛺ *INSTALAÇÕES DE HOJE:*\n";
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

            $msg .= "📲 Ao concluir cada montagem, acesse o sistema no celular para registrar *'Tenda instalada'*!";

            if ($this->whatsapp->enviarMensagem($montador->whatsapp, $msg)) {
                Cache::put($lockKey, true, $hoje->copy()->endOfDay()->addHours(2));
                $this->info("Lembrete diário enviado para {$montador->name}");
                $enviados++;
            } else {
                Log::warning("Falha ao enviar lembrete diário para {$montador->name} ({$montador->whatsapp}).");
            }
        }

        if ($enviados === 0) {
            $this->info("Nenhuma instalação agendada para hoje.");
        }
    }
}
