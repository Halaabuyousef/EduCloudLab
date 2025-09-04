<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\ContactMessage;

use App\Http\Controllers\Controller;
use App\Mail\ContactMessageReceived;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\ContactRequest;


class ContactController extends Controller
{
    public function store(ContactRequest $request)
    {
        $data = $request->validated();
        $data['ip'] = $request->ip();
        $data['user_agent'] = (string) $request->userAgent();

        $msg = ContactMessage::create($data);

     
        try {
            if (config('mail.default') !== 'log' && config('mail.mailers.smtp')) {
                Mail::to(config('mail.from.address'))
                    ->queue(new ContactMessageReceived($msg));
            }
        } catch (\Throwable $e) {
          
            report($e);
        }

        return response()->json([
            'success' => true,
            'message' => 'Message received. We will contact you soon.',
            'data' => [
                'id' => $msg->id,
                'created_at' => $msg->created_at,
            ]
        ], 201);
    }
}
