<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use App\Models\Reservation;

class ReservationWillStartSoon extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected Reservation $reservation,
        protected int $minutes
    ) {}

    public function via($notifiable): array
    {
        return ['mail', 'database', 'broadcast'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Reservation Reminder - EduCloudLab')
            ->greeting('Hello ' . $notifiable->name . ' 👋')
            ->line("Your reservation for {$this->reservation->experiment->title} will start in {$this->minutes} minutes.")
            ->action('View Reservation', url("/admin/reservations/{$this->reservation->id}"))
            ->line('Thank you for using EduCloudLab!');
    }

    public function toDatabase($notifiable): array
    {
        return [
            'type'  => 'reservation.will_start',
            'title' => 'Upcoming Reservation',
            'body'  => "Your reservation for {$this->reservation->experiment->title} will start in {$this->minutes} minutes.",
            'url'   => url("/admin/reservations/{$this->reservation->id}"),
        ];
    }

    public function toBroadcast($notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toDatabase($notifiable));
    }
}
