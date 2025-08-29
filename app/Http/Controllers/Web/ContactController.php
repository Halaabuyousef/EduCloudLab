<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContactRequest;
use App\Mail\ContactMessageReceived;
use App\Models\ContactMessage;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function create()
    {
        return view('web.contact');
    }

    public function store(ContactRequest $request)
    {
        // save
        $msg = ContactMessage::create([
            'name'       => $request->name,
            'email'      => $request->email,
            'subject'    => $request->subject,
            'message'    => $request->message,
            'ip'         => $request->ip(),
            'user_agent' => (string) request()->header('User-Agent'),
        ]);

        // email
        $inbox = config('mail.contact_inbox', env('CONTACT_INBOX', config('mail.from.address')));
        if ($inbox) {
            Mail::to($inbox)->send(new ContactMessageReceived($msg));
        }

        // flash + redirect
        return redirect()
            ->route('contact.create')
            ->with(['type' => 'success', 'msg' => __('Your message was sent successfully!')]);
    }
}
