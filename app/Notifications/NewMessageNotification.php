<?php

namespace App\Notifications;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewMessageNotification extends Notification
{
    use Queueable;

    public function __construct(public Message $message) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'New project message',
            'message' => $this->message->sender->name.' sent a message about "'.$this->message->project->title.'".',
            'url' => route('messages.show', $this->message->project_id, absolute: false),
        ];
    }
}
