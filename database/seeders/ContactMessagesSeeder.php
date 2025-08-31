<?php

namespace Database\Seeders;

use App\Models\ContactMessage;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ContactMessagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $emails = [
            ['name' => 'Omar Matter', 'email' => 'halaaboyousef2002@gmail.com'],
            ['name' => 'hala',        'email' => 'halaaboyousef02@gmail.com'],
            ['name' => 'Insaf',       'email' => 'roumyinsaf119@gmail.com'],
            ['name' => 'afnan',       'email' => 'afnanabdelfattahbadwan@gmail.com'],
        ];

        foreach ($emails as $i => $u) {
            ContactMessage::create([
                'name'    => $u['name'],
                'email'   => $u['email'],
                'subject' => "Hello from {$u['name']}",
                'message' => "This is a test message sent by {$u['name']} with email {$u['email']}.\n\nJust for dashboard testing.",
                'ip'      => "127.0.0." . ($i + 1),
                'user_agent' => "SeederTestBrowser/1.0",
                'created_at' => now()->subMinutes($i * 5),
                'updated_at' => now()->subMinutes($i * 5),
                'read_at' => $i % 2 === 0 ? null : now(), 
            ]);
        }
    
    
    }
}
