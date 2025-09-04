<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Reservation;
class ReservationCreatedNotification extends Notification implements ShouldQueue
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
        return ['database', 'fcm'];
    }
    public function toDatabase($notifiable)
    {
        return [
            'reservation_id' => $this->reservation->id,
            'experiment_id'  => $this->reservation->experiment_id,
            'status'         => $this->reservation->status,
        ];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toFcm($notifiable)
    {
        return [
            'title' => "Reservation Confirmed 🎉",
            'body'  => "Your reservation #{$this->reservation->id} has been approved.",
            'data'  => [
                'reservation_id' => (string) $this->reservation->id,
                'status'         => $this->reservation->status,
            ],
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    // public function toArray($notifiable)
    // {
    //     return [
    //         'title' => 'Reservation Approved',
    //         'body'  => "Your reservation #{$this->reservation->id} has been approved.",
    //         'url'   => route('admin.reservations.show', $this->reservation->id),
    //         'icon'  => 'fas fa-calendar-check',
    //         'meta'  => [
    //             'reservation_id' => $this->reservation->id,
    //             'status' => $this->reservation->status,
    //         ],
    //     ];
    // }
}
