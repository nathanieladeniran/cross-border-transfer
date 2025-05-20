<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BusinessAccountCreation extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    protected $business_name, $user_name;
    public function __construct($business_name, $user_name)
    {
        $this->business_name = $business_name;
        $this->user_name = $user_name;
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
            ->subject('Business Account Created')
            ->greeting('Hello, ' . $this->user_name)
            ->line('We are excited to inform you that your business profile ' . strtoupper($this->business_name) . ' has been created successfully. 
            Your business profile once verified will allow you to initiate transactions as a business entity rather than as an individual..')
            ->line('We are here to support you every step of the way. If you have any questions or need help, please do not hesitate to reach out to our support team.')
            ->line('Thank you for trusting us!');
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
