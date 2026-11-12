<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Lembrete toda segunda-feira às 07h00 com todas as instalações da semana por montador
Schedule::command('lembretes:whatsapp --tipo=semana')
    ->weeklyOn(1, '07:00')
    ->timezone('America/Sao_Paulo')
    ->withoutOverlapping();

// Lembrete diário às 07h00 (terça a domingo) com as instalações do dia por montador
// Na segunda-feira não envia o diário porque o lembrete semanal já envia todas as instalações da semana
Schedule::command('lembretes:whatsapp --tipo=dia')
    ->days([0, 2, 3, 4, 5, 6])
    ->at('07:00')
    ->timezone('America/Sao_Paulo')
    ->withoutOverlapping();
