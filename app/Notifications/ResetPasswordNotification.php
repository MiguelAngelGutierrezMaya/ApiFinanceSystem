<?php

namespace App\Notifications;

use App\Person;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    /**
     * @var Token_password
     */
    protected $token_password;

    /**
     * @var Passreset_code
     */
    protected $passreset_code;

    /**
     * @var Person
     */
    protected $person;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Person $person, $passreset_code, $token_password)
    {
        $this->person = $person;
        $this->passreset_code = $passreset_code;
        $this->token_password = $token_password;
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
            ->subject('Solicitud de reestablecimiento de contraseña')
            ->greeting('Estimado Usuario ' . $this->person->names . ' ' . $this->person->surnames)
            ->line('Recibes este email porque se solicitó un reestablecimiento de contraseña para tu cuenta.')
            ->line('Copia y pega las siguientes lineas en la página de restablecimiento en tu app:')
            ->line('Token de usuario: ' . $this->passreset_code)
            ->line('Contraseña de acceso: ' . $this->token_password)
            ->line('Si no realizaste esta petición, puedes ignorar este correo y nada habrá cambiado.')
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
