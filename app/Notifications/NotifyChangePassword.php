<?php

namespace App\Notifications;

use App\Person;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class NotifyChangePassword extends Notification
{
    use Queueable;

    /**
     * @var User
     */
    protected $person;

    /**
     * Create a new notification instance.
     *
     * @param User $user
     */
    public function __construct(Person $person)
    {
        $this->person = $person;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->from('miguel.gutierrez@correounivalle.edu.co')
            ->subject('Cambio de contraseña')
            ->greeting('Estimado Usuario ' . $this->person->names . ' ' . $this->person->surnames)
            ->line('Recibes este email porque has cambiado tu contraseña en el sistema desde tu app.')
            ->line('Si no realizaste esta petición, por favor comunicate con soporte para proteger tu cuenta.')
            ->salutation('¡Saludos!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
