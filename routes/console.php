<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Lembrete toda segunda-feira às 08h00 com as montagens da semana
Schedule::command('lembretes:whatsapp --tipo=semana')
    ->weeklyOn(1, '08:00')
    ->timezone('America/Sao_Paulo');

// Lembrete diário às 07h00 no dia de cada montagem
Schedule::command('lembretes:whatsapp --tipo=dia')
    ->dailyAt('07:00')
    ->timezone('America/Sao_Paulo');
