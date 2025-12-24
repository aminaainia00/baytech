<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UpdateBookRequestNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
     protected $house;
    public function __construct($house)
    {
        $this->house=$house;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */


    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */

    public function toDatabase(object $notifiable): array
    {
        return [
        "title"=>'New Notification',
        "body"=>'you have new update book request '.$this->house->title. ' house',
        ];
    }
}
