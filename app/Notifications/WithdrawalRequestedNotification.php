<?php

namespace App\Notifications;

use App\Models\WithdrawalRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class WithdrawalRequestedNotification extends Notification
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
            'title' => 'New withdrawal request',
            'message' => $this->withdrawal->user->name.' requested $'.number_format((float) $this->withdrawal->amount, 2).'.',
            'url' => route('admin.withdrawals.index', ['status' => 'pending'], absolute: false),
        ];
    }
}
