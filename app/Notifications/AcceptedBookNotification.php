<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AcceptedBookNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
     protected $Booking;
    public function __construct($Booking)
    {
        $this->Booking=$Booking;
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
  /*  public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }*/
         public function toDatabase(object $notifiable): array
    {
        return [
        "title"=>'New Notification',
        "body"=>'your book is accepted',
        "data"=>['title_house'=>$this->Booking->house->title,
        'start_date'=>$this->Booking->start_date,
        'end_date'=>$this->Booking->end_date

         ] ];
    }
}
