<?php

namespace App\Notifications;

use App\Models\WithdrawalRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class WithdrawalStatusNotification extends Notification
{
    use Queueable;

    public function __construct(public WithdrawalRequest $withdrawal) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Withdrawal '.ucfirst($this->withdrawal->status),
            'message' => 'Your $'.number_format((float) $this->withdrawal->amount, 2).' withdrawal is now '.$this->withdrawal->status.'.',
            'url' => route('wallet.index', absolute: false),
        ];
    }
}
