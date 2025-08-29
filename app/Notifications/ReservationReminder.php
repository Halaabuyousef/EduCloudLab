<?php

namespace App\Notifications;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ReservationReminder extends Notification implements ShouldQueue
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
            'title' => 'Reminder: Your session starts in 10 minutes',
            'body'  => "Your reservation for {$this->reservation->experiment->title} will start at {$this->reservation->start_time}.",
            'reservation_id' => $this->reservation->id,
            'type' => 'reservation.reminder',
        ];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Reminder: Your session starts in 10 minutes')
            ->line("Your reservation for {$this->reservation->experiment->title} will start at {$this->reservation->start_time}.")
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
