<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyAdmin extends Notification
{
    use Queueable;
    public $globalData;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(array $datos)
    {
        $this->globalData = $datos;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get last Insert data into Table
     */
    public function toDatabase($notifiable)
    {
        return [
            'datos' => $this->globalData
        ];
    }

    /*public function toBroadcast($notifiable)
    {
        return [
            'data' => [
                'datos' => $this->globalData
            ]
        ];
    }*/

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
