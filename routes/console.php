<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('expenses:send-due-reminders')
    ->dailyAt(env('DUE_REMINDER_AT', '08:00'))
    ->timezone(config('app.timezone'))
    ->withoutOverlapping();

Artisan::command('expenses:reminder-status', function () {
    $lastRunAt = Cache::get('expenses_due_reminders:last_run_at');
    $lastTargetDate = Cache::get('expenses_due_reminders:last_target_date');
    $lastNotifiedUsers = (int) Cache::get('expenses_due_reminders:last_notified_users', 0);
    $lastTotalItems = (int) Cache::get('expenses_due_reminders:last_total_items', 0);
    $dailyAt = env('DUE_REMINDER_AT', '08:00');
    $timezone = config('app.timezone');

    $this->info('Upcoming due reminders status');
    $this->line("Scheduled: daily at {$dailyAt} ({$timezone})");
    $this->line('Last run at: '.($lastRunAt ?: 'never'));
    $this->line('Last target date: '.($lastTargetDate ?: 'n/a'));
    $this->line("Last notified users: {$lastNotifiedUsers}");
    $this->line("Last due items sent: {$lastTotalItems}");
})->purpose('Show status of upcoming due date reminder scheduler');
