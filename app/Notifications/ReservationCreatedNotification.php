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
        return ['database', 'mail'];
    }
    public function toDatabase($notifiable)
    {
        return [
            'title'          => 'تم الحجز',
            'body'           => "التجربة: {$this->reservation->experiment->title} من {$this->reservation->starts_at}",
            'reservation_id' => $this->reservation->id,
            'type'           => 'reservation.approved',

            // أهم سطرين:
            'url'            => route('admin.reservations.show', $this->reservation->id),
            'route'          => 'admin.reservations.show',
        ];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject('تمت الموافقة على الحجز')
            ->line("التجربة: {$this->reservation->experiment->title}")
            ->line("من: {$this->reservation->starts_at} إلى: {$this->reservation->ends_at}")
            ->action('عرض الحجز', route('admin.reservations.show', $this->reservation->id));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray($notifiable)
    {
        return [
            'title' => 'Reservation Approved',
            'body'  => "Your reservation #{$this->reservation->id} has been approved.",
            'url'   => route('admin.reservations.show', $this->reservation->id),
            'icon'  => 'fas fa-calendar-check',
            'meta'  => [
                'reservation_id' => $this->reservation->id,
                'status' => $this->reservation->status,
            ],
        ];
    }
}
