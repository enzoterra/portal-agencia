<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\Payment;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// 1. Sincroniza Google Calendar da agência — a cada 5 minutos
Schedule::command('calendar:sync')
    ->everyFiveMinutes()
    ->withoutOverlapping()
    ->runInBackground();

// 2. Marca pagamentos vencidos — 1x por dia às 00:05
Schedule::call(function () {
    Payment::where('status', 'pending')
        ->whereDate('due_date', '<', today())
        ->update(['status' => 'overdue']);
})->dailyAt('00:05');
