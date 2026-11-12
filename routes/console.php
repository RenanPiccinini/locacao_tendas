<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Lembrete toda segunda-feira às 07h00 com as montagens da semana
Schedule::command('lembretes:whatsapp --tipo=semana')
    ->weeklyOn(1, '07:00')
    ->timezone('America/Sao_Paulo')
    ->withoutOverlapping();

// Lembrete diário às 07h00 no dia de cada montagem e retirada
Schedule::command('lembretes:whatsapp --tipo=dia')
    ->dailyAt('07:00')
    ->timezone('America/Sao_Paulo')
    ->withoutOverlapping();
