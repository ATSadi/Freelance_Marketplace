<?php

namespace App\Notifications;

use App\Models\Proposal;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ProposalAcceptedNotification extends Notification
{
    use Queueable;

    public function __construct(public Proposal $proposal) {}

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
            'title' => 'Proposal accepted',
            'message' => 'Your proposal for "'.$this->proposal->project->title.'" was accepted. The project is now in progress.',
            'url' => route('projects.show', $this->proposal->project_id, absolute: false),
        ];
    }
}
