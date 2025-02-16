<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Mail;

class SendEmail extends Component
{
    public $to, $subject, $body;

    public function send()
    {
        Mail::raw($this->body, function ($message) {
            $message->to($this->to)
                    ->subject($this->subject);
        });

        session()->flash('message', 'Email đã gửi thành công!');
    }

    public function render()
    {
        return view('livewire.send-email');
    }
}
