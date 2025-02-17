<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Webklex\IMAP\Facades\Client;

class Mailbox extends Component
{
    public $emails = [];

    public function mount()
    {
        $client = Client::account('gmail');

        // $folder = $client->getFolder('INBOX');
        // $messages = $folder->messages()->limit(10)->get();

        // foreach ($messages as $message) {
        //     $this->emails[] = [
        //         'from' => $message->getFrom()[0]->mail,
        //         'subject' => $message->getSubject(),
        //         'date' => $message->getDate()->format('Y-m-d H:i:s'),
        //         'body' => $message->getTextBody(),
        //     ];
        // }
        try {
            $client->connect();
            Log::info('connecting successfully');
            $folder = $client->getFolder('INBOX');
            $messages = $folder->query()->unseen()->setFetchOrder('desc')->limit(10)->get();

            foreach ($messages as $message) {
                Log::info("📧 Email mới nhận được:");
                Log::info("📌 Tiêu đề: " . $message->getSubject());
                Log::info("✉️ Người gửi: " . $message->getFrom()[0]->mail);
                Log::info("📅 Ngày nhận: " . $message->getDate());
                Log::info("📩 Nội dung:\n" . $message->getTextBody());
                Log::info("---------------------------------------");
            }
        } catch (\Exception $e) {
            Log::error('Error connecting: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.mailbox');
    }
}
