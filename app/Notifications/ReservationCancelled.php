<?php

namespace App\Notifications;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ReservationCancelled extends Notification implements ShouldQueue
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

    /**
     * Get the mail representation of the notification.
     */
    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Your reservation was cancelled',
            'body'  => "Your reservation for {$this->reservation->experiment->title} has been cancelled.",
            'reservation_id' => $this->reservation->id,
            'type' => 'reservation.cancelled',
        ];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Your reservation was cancelled')
            ->line("Your reservation for {$this->reservation->experiment->title} has been cancelled.")
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
