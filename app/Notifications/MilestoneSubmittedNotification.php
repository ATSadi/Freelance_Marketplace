<?php

namespace App\Notifications;

use App\Models\Milestone;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class MilestoneSubmittedNotification extends Notification
{
    use Queueable;

    public function __construct(public Milestone $milestone) {}

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
            'title' => 'Milestone submitted',
            'message' => 'Milestone "'.$this->milestone->title.'" on "'.$this->milestone->project->title.'" is ready for review.',
            'url' => route('projects.show', $this->milestone->project_id, absolute: false),
        ];
    }
}
