<?php

namespace App\Http\Controllers\Admin;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ContactMessageController extends Controller
{
    /**
     * Handle the incoming request.
     */
    //     public function __invoke(Request $request)
    //     {
    //         $messages = ContactMessage::latest()->paginate(15);
    //         return view('admin.contacts.index', compact('messages'));
    //     }
    // قائمة + بحث + فلترة (read=new/read/all)
    public function __invoke(Request $r)
    {
        $q      = trim((string)$r->get('q', ''));
        $read   = $r->get('read'); // 'all' | 'new' | 'read'
        $per    = (int)($r->get('per', 15));
        $per    = max(5, min($per, 100));

        $messages = ContactMessage::query()
            ->when($q, fn($qq) => $qq->where(function ($w) use ($q) {
                $w->where('name', 'like', "%$q%")
                    ->orWhere('email', 'like', "%$q%")
                    ->orWhere('subject', 'like', "%$q%")
                    ->orWhere('message', 'like', "%$q%");
            }))
            ->when($read === 'new',  fn($qq) => $qq->whereNull('read_at'))
            ->when($read === 'read', fn($qq) => $qq->whereNotNull('read_at'))
            ->latest()
            ->paginate($per)
            ->withQueryString();

        $newCount = ContactMessage::whereNull('read_at')->count();

        return view('admin.contacts.index', compact('messages', 'q', 'read', 'per', 'newCount'));
    }

    public function show(ContactMessage $message)
    {
        if (is_null($message->read_at)) $message->forceFill(['read_at' => now()])->save();
        return view('admin.contacts.show', compact('message'));
    }

    public function markRead(ContactMessage $message)
    {
        if (is_null($message->read_at)) $message->update(['read_at' => now()]);
        return back()->with(['type' => 'success', 'msg' => 'Marked as read.']);
    }

    public function markUnread(ContactMessage $message)
    {
        if (!is_null($message->read_at)) $message->update(['read_at' => null]);
        return back()->with(['type' => 'success', 'msg' => 'Marked as unread.']);
    }

    public function destroy(ContactMessage $message)
    {
        $message->delete();
        return redirect()->route('admin.contacts.index')->with(['type' => 'success', 'msg' => 'Message deleted.']);
    }

    // ردّ سريع بالإيميل
    public function reply(Request $r, ContactMessage $message)
    {
        $data = $r->validate([
            'subject' => ['nullable', 'string', 'max:190'],
            'body'    => ['required', 'string', 'max:5000'],
        ]);
        $subject = $data['subject'] ?? ('Re: ' . ($message->subject ?: 'Your message'));

        try {
            Mail::raw($data['body'], fn($m) => $m->to($message->email)->subject($subject));
            return back()->with(['type' => 'success', 'msg' => 'Reply sent.']);
        } catch (\Throwable $e) {
            \Log::warning('Reply mail failed: ' . $e->getMessage());
            return back()->with(['type' => 'warning', 'msg' => 'Mail send failed (logged).']);
        }
    }

    // CSV تصدير
    public function export(Request $r): StreamedResponse
    {
        $file = 'contact_messages_' . now()->format('Ymd_His') . '.csv';
        $query = ContactMessage::query()
            ->when($r->filled('from'), fn($q) => $q->whereDate('created_at', '>=', $r->date('from')))
            ->when($r->filled('to'),   fn($q) => $q->whereDate('created_at', '<=', $r->date('to')))
            ->when($r->get('read') === 'new',  fn($q) => $q->whereNull('read_at'))
            ->when($r->get('read') === 'read', fn($q) => $q->whereNotNull('read_at'))
            ->orderByDesc('id');

        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['id', 'name', 'email', 'subject', 'message', 'read_at', 'ip', 'user_agent', 'created_at']);
            $query->chunk(500, function ($chunk) use ($out) {
                foreach ($chunk as $m) {
                    fputcsv($out, [
                        $m->id,
                        $m->name,
                        $m->email,
                        $m->subject,
                        preg_replace("/\r?\n/", " ", $m->message),
                        optional($m->read_at)->toDateTimeString(),
                        $m->ip,
                        $m->user_agent,
                        $m->created_at->toDateTimeString()
                    ]);
                }
            });
            fclose($out);
        }, $file, ['Content-Type' => 'text/csv']);
    }

    // Badge عداد غير المقروء
    public function badge()
    {
        return response()->json(['unread' => ContactMessage::whereNull('read_at')->count()]);
    }
}
