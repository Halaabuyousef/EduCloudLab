<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Exception\MessagingException;
use Kreait\Firebase\Exception\FirebaseException;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class FcmService
{
    protected \Kreait\Firebase\Contract\Messaging $messaging;

    public function __construct()
    {
        $factory = (new Factory)->withServiceAccount(base_path(env('FIREBASE_CREDENTIALS')));
        $this->messaging = $factory->createMessaging();
    }

    /**
     * أرسل إشعار لتوكن واحد.
     */
    public function sendToToken(string $token, string $title, string $body, array $data = []): bool
    {
        try {
            $message = CloudMessage::withTarget('token', $token)
                ->withNotification(Notification::create($title, $body))
                ->withData($data);

            $this->messaging->send($message);
            return true;
        } catch (MessagingException | FirebaseException $e) {
            Log::error('FCM sendToToken failed', [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * أرسل إشعار لجميع أجهزة المستخدم (جدول device_tokens مرتبط بالمستخدم).
     * يعيد عدد الأجهزة التي تم الإرسال لها بنجاح.
     */
    public function sendToUser(User $user, string $title, string $body, array $data = []): int
    {
        $sent = 0;

        // تأكد أن لديك علاقة deviceTokens() على موديل User
        foreach ($user->deviceTokens as $dt) {
            if ($this->sendToToken($dt->token, $title, $body, $data)) {
                $sent++;
            }
        }

        return $sent;
    }
}
