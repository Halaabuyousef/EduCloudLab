<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ForgetPasswordNotification extends Notification
{
    use Queueable;
    protected $token , $guard ;

    /**
     * Create a new notification instance.
     */
      public function __construct(string $token , string $guard)
    {
        $this->token = $token ;
        $this->guard = $guard ;
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
        $url = url("/{$this->guard}/reset-password/{$this->token}?email=" . urlencode($notifiable->email));
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', $url)
                    ->line('Thank you for using our application!');
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
