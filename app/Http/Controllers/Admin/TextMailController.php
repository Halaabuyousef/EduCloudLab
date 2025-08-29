<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TextMail;
use App\Models\Admin;
use App\Notifications\TextCreatedNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Http\Request;

class TextMailController extends Controller
{
    public function store(Request $request)
    {
        $request->validate(['text' => 'required|string']);

        // خزّن الرسالة في الجدول
        $text = TextMail::create(['text' => $request->text]);

        // أرسلها لكل الأدمنز
        $admins = Admin::all();
        Notification::send($admins, new TextCreatedNotification($text));

        return back()->with('msg', 'تم إرسال الإشعار بنجاح');
    }
}
