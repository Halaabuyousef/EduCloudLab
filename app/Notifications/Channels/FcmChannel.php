<?php

namespace App\Notifications\Channels;

use App\Services\FcmService;

class FcmChannel
{
    protected $fcm;

    public function __construct(FcmService $fcm)
    {
        $this->fcm = $fcm;
    }

    public function send($notifiable, $notification)
    {
        if (!method_exists($notification, 'toFcm')) {
            return;
        }

        $message = $notification->toFcm($notifiable);

    
        $this->fcm->sendToUser(
            $notifiable,
            $message['title'],
            $message['body'],
            $message['data'] ?? []
        );
    }
}
