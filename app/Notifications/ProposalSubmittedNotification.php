<?php

namespace App\Notifications;

use App\Models\Proposal;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ProposalSubmittedNotification extends Notification
{
    use Queueable;

    public function __construct(public Proposal $proposal)
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
            'title' => 'New proposal received',
            'message' => $this->proposal->freelancer->name.' submitted a proposal on "'.$this->proposal->project->title.'".',
            'url' => route('projects.show', $this->proposal->project_id, absolute: false),
        ];
    }
}
