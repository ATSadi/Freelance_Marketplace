<?php

namespace App\Notifications;

use App\Models\Dispute;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DisputeOpenedNotification extends Notification
{
    use Queueable;

    public function __construct(public Dispute $dispute)
    {
    }

    /**
     * @return list<string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'New dispute opened',
            'message' => 'A dispute was opened on "'.$this->dispute->project->title.'": '.$this->dispute->reason,
            'url' => route('disputes.show', $this->dispute, absolute: false),
        ];
    }
}
