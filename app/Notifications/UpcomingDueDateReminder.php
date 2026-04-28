<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UpcomingDueDateReminder extends Notification
{
    use Queueable;

    /**
     * @param  array<int, array{month_label:string,year:int,label:string,amount:float,due_date:string,key:string}>  $items
     */
    public function __construct(
        public string $dueDate,
        public array $items
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject("Upcoming due date reminder ({$this->dueDate})")
            ->greeting("Hi {$notifiable->name},")
            ->line("You have unpaid expense(s) due tomorrow ({$this->dueDate}).");

        foreach ($this->items as $item) {
            $label = trim($item['label']) !== '' ? $item['label'] : 'Unnamed expense';
            $amount = number_format((float) $item['amount'], 2);
            $message->line("• {$label} — ₱{$amount} ({$item['month_label']} {$item['year']})");
        }

        return $message
            ->action('Open Dashboard', url('/dashboard'))
            ->line('If you already paid these, you can ignore this email.');
    }
}

