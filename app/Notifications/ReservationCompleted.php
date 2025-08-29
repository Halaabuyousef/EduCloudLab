<?php

namespace App\Notifications;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ReservationCompleted extends Notification implements ShouldQueue
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
            'title' => 'Your session has ended',
            'body'  => "Your reservation for {$this->reservation->experiment->title} has ended successfully.",
            'reservation_id' => $this->reservation->id,
            'type' => 'reservation.completed',
        ];
    }
    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject('Reservation Completed - EduCloudLab')
            ->greeting('Hello ' . $notifiable->name . ' 👋')
            ->line("Your reservation for {$this->reservation->experiment->title} has ended successfully.")
            ->action('View Reservation', url("/admin/reservations/{$this->reservation->id}"))
            ->line('Thank you for using EduCloudLab!');
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
