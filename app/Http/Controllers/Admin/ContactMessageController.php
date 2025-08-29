<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\ContactMessage;
use App\Http\Controllers\Controller;

class ContactMessageController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $messages = ContactMessage::latest()->paginate(15);
        return view('admin.contacts.index', compact('messages'));
    }
}
