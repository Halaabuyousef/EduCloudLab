<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Reservation;
class ReservationStarted extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Reservation $reservation)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }
    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Your session has started',
            'body'  => "Your reservation for {$this->reservation->experiment->title} is now active ({$this->reservation->start_time} → {$this->reservation->end_time}).",
            'reservation_id' => $this->reservation->id,
            'type' => 'reservation.started',
        
        ];
    }
    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your session has started')
            ->line("Your reservation for {$this->reservation->experiment->title} is now active.")
            ->line("Duration: {$this->reservation->start_time} → {$this->reservation->end_time}")
            ->action('View Reservation', route('admin.reservations.show', $this->reservation->id));
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
