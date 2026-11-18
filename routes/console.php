<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Lembrete toda segunda-feira às 07h00 com todas as instalações e desmontagens da semana por montador
Schedule::command('lembretes:whatsapp --tipo=semana')
    ->weeklyOn(1, '07:00')
    ->timezone('America/Sao_Paulo')
    ->withoutOverlapping();

// NOTIFICAÇÃO DIÁRIA DESATIVADA TEMPORARIAMENTE (Mantendo apenas a notificação semanal de segunda-feira)
// Caso seja necessário reativar no futuro, basta descomentar o bloco abaixo:
/*
Schedule::command('lembretes:whatsapp --tipo=dia')
    ->days([0, 2, 3, 4, 5, 6])
    ->at('07:00')
    ->timezone('America/Sao_Paulo')
    ->withoutOverlapping();
*/
