<?php

namespace App\Livewire;

use Livewire\Component;
use Webklex\IMAP\Facades\Client;

class Mailbox extends Component
{
    public $emails = [];

    public function mount()
    {
        $client = Client::account('gmail');
        $client->connect();

        $folder = $client->getFolder('INBOX');
        $messages = $folder->messages()->limit(10)->get();

        foreach ($messages as $message) {
            $this->emails[] = [
                'from' => $message->getFrom()[0]->mail,
                'subject' => $message->getSubject(),
                'date' => $message->getDate()->format('Y-m-d H:i:s'),
                'body' => $message->getTextBody(),
            ];
        }
    }

    public function render()
    {
        return view('livewire.mailbox');
    }
}
