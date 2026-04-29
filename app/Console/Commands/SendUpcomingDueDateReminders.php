<?php

namespace App\Console\Commands;

use App\Models\MonthlyPlannerEntry;
use App\Models\User;
use App\Notifications\UpcomingDueDateReminder;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class SendUpcomingDueDateReminders extends Command
{
    protected $signature = 'expenses:send-due-reminders {--date= : Override target date (YYYY-MM-DD)}';

    protected $description = 'Email users about unpaid expenses due tomorrow.';

    public function handle(): int
    {
        $tz = config('app.timezone') ?: 'UTC';

        $targetDate = $this->option('date')
            ? CarbonImmutable::parse((string) $this->option('date'), $tz)->startOfDay()
            : CarbonImmutable::now($tz)->addDay()->startOfDay();

        $targetDateString = $targetDate->toDateString();

        $this->info("Scanning for unpaid expenses due on {$targetDateString} ({$tz})...");

        $notifiedUsers = 0;
        $totalItems = 0;

        User::query()
            ->select(['id', 'name', 'email'])
            ->whereNotNull('email')
            ->chunkById(200, function ($users) use ($targetDateString, $tz, &$notifiedUsers, &$totalItems) {
                foreach ($users as $user) {
                    /** @var \App\Models\User $user */
                    $entries = MonthlyPlannerEntry::query()
                        ->where('user_id', $user->id)
                        ->whereNotNull('expenses')
                        ->get(['id', 'month_label', 'year', 'expenses']);

                    $items = [];

                    foreach ($entries as $entry) {
                        $expenses = is_array($entry->expenses) ? $entry->expenses : [];

                        foreach ($expenses as $expenseIndex => $expense) {
                            if (!is_array($expense)) {
                                continue;
                            }

                            $paid = (bool) ($expense['paid'] ?? false);
                            if ($paid) {
                                continue;
                            }

                            $dueDateRaw = $expense['due_date'] ?? null;
                            if (!$dueDateRaw) {
                                continue;
                            }

                            try {
                                $dueDate = CarbonImmutable::parse((string) $dueDateRaw, $tz)->toDateString();
                            } catch (\Throwable) {
                                continue;
                            }

                            if ($dueDate !== $targetDateString) {
                                continue;
                            }

                            $items[] = [
                                'month_label' => (string) $entry->month_label,
                                'year' => (int) ($entry->year ?? CarbonImmutable::now($tz)->year),
                                'label' => (string) ($expense['label'] ?? ''),
                                'amount' => (float) ($expense['amount'] ?? 0),
                                'due_date' => $dueDate,
                                'key' => "{$entry->id}:{$expenseIndex}",
                            ];
                        }
                    }

                    if (count($items) === 0) {
                        continue;
                    }

                    $user->notify(new UpcomingDueDateReminder(
                        dueDate: $targetDateString,
                        items: $items
                    ));

                    $notifiedUsers++;
                    $totalItems += count($items);
                }
            });

        $this->info("Done. Notified {$notifiedUsers} user(s) about {$totalItems} due item(s).");

        Cache::forever('expenses_due_reminders:last_run_at', now()->toIso8601String());
        Cache::forever('expenses_due_reminders:last_target_date', $targetDateString);
        Cache::forever('expenses_due_reminders:last_notified_users', $notifiedUsers);
        Cache::forever('expenses_due_reminders:last_total_items', $totalItems);

        return self::SUCCESS;
    }
}

