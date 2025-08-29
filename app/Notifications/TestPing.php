<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TestPing extends Notification
{
    use Queueable;

    public function __construct(public string $msg = 'Ping from Test')
    {
        //
    }

    public function via($notifiable)
    {
        return ['database']; // نخزن في DB فقط
    }

    public function toArray($notifiable)
    {
        return [
            'title' => 'Test Notification',
            'body'  => $this->msg,
            'url'   => route('admin.dashboard'), // عدّلها لو حابب
        ];
    }
}
