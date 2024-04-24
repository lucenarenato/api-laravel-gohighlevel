<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewMemberNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct($details)
    {
        $this->details = $details;
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
            ->subject('Welcome to '.env('APP_NAME'))
            ->from(env('NO_REPLY_EMAIL'), env('APP_NAME'))
            ->greeting('Dear '.$this->details['name'].',')
            ->line('Congratulations you have been successfully registrered on '.env('APP_NAME'))
            ->line('Below are the credentials to log into '.env('APP_NAME'))
            ->line('Email: '.$this->details['email'])
            ->line('Password: '.$this->details['password'])
            ->action('Login Now', url(env('LOGIN_URL')))
            ->line('Thankyou for using '.env('APP_NAME').'!');
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
