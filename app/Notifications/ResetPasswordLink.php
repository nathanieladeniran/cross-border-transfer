<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordLink extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */

    public $token;
    public $email;

    public function __construct($email, $token)
    {
        $this->email = $email;
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Account Recovery")
            ->line('We received a request to reset your password for your account. If you did not make this request, please ignore this email.')
            ->line('To reset your password, please click the link below:')
            ->action('Reset Password', url(config('app.url') . route('password.email', ['token=' . $this->token, 'email=' . $this->email], false)))
            ->line('This link will expire within 1 hour for security reasons. If the link does not work, please copy and paste it into your web browser')
            ->line('Thank you for using our service!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
