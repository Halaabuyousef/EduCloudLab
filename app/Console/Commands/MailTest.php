<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class MailTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:test {to?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a test email via configured SMTP';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $to = $this->argument('to') ?: config('mail.contact_inbox');

        if (!$to) {
            $this->error('❌ No recipient found. Set CONTACT_INBOX in .env or pass an email.');
            return 1;
        }

        Mail::raw('This is a test email from Laravel via Gmail SMTP', function ($m) use ($to) {
            $m->to($to)->subject('Laravel Gmail Test');
        });

        $this->info("✅ Test email sent to {$to}");
        return 0;
    
    }
}
