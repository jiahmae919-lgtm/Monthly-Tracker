<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('expenses:send-due-reminders')
    ->dailyAt(env('DUE_REMINDER_AT', '08:00'))
    ->timezone(config('app.timezone'))
    ->withoutOverlapping();
