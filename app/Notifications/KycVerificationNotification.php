<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class KycVerificationNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public $status;
    public $reason;

    public function __construct($status, $reason = '')
    {
        $this->status = $status;
        $this->reason = $reason;
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

    public function declined($notifiable){
        return (new MailMessage)
            ->subject('KYC Verification Declined')
            ->greeting('Hello ')
            ->line('We wish to inform you that, your uploaded profile update request was declined.')
            ->line('Reason: '. $this->reason)
            ->line('')
            ->line('')
            ->line('Sign in to your account, go-to user profile to update you update your profile or contact our compliance team at compliance@cosmoremit.com.au.')
            ->action('Sign in', route('signin.index'))
            ->line('Thank you for choosing us!');
    }

    public function success($notifiable)
    {
        return (new MailMessage)
            ->subject('KYC Verification Successful')
            ->greeting('Hello ' . $notifiable->profile->fullname())
            ->line('We wish to inform you that, your uploaded profile update request was successful. 🎉')
            ->line('')
            ->line('')
            ->line('Sign in to your account, to continue enjoying our service. 😊')
            ->action('Sign in', route('signin.index'))
            ->line('Thank you for choosing us!');
    }

    public function pending($notifiable)
    {
        return (new MailMessage)
            ->subject('KYC Verification Pending')
            ->greeting('Hello ' . $notifiable->profile->fullname())
            ->line('Thank you for making corrections on your profile.
                    Please wait for our compliance team\'s approval')
            ->line('')
            ->line('')
            ->line('Sign in to your account, to continue enjoying our service. 😊')
            ->action('Sign in', route('signin.index'))
            ->line('Thank you for choosing us!');
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        if ($this->status === 'verification.declined')
        {
            return $this->declined($notifiable);
        }
        else if ($this->status === 'verification.accepted') {
            return $this->success($notifiable);
        }
        return $this->pending($notifiable);
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
