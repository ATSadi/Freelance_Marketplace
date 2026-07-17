<?php

namespace App\Notifications;

use App\Models\Milestone;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class MilestoneApprovedNotification extends Notification
{
    use Queueable;

    public function __construct(public Milestone $milestone)
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
            'title' => 'Milestone approved & paid',
            'message' => 'Milestone "'.$this->milestone->title.'" was approved and escrow payment was released.',
            'url' => route('projects.show', $this->milestone->project_id, absolute: false),
        ];
    }
}
