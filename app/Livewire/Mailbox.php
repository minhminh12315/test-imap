<?php

namespace App\Livewire;

use App\Models\Mail;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Webklex\IMAP\Facades\Client;
use Illuminate\Support\Facades\DB;

class Mailbox extends Component
{
    public $emails = [];

    public function mount()
    {
        // $client = Client::account('gmail');

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
        // try {
        //     $client->connect();
        //     Log::info('connecting successfully');
        //     $folder = $client->getFolder('INBOX');
        //     $messages = $folder->query()->unseen()->setFetchOrder('desc')->limit(10)->get();

        //     foreach ($messages as $message) {
        //         Log::info("📧 Email mới nhận được:");
        //         Log::info("📌 Tiêu đề: " . $message->getSubject());
        //         Log::info("✉️ Người gửi: " . $message->getFrom()[0]->mail);
        //         Log::info("📅 Ngày nhận: " . $message->getDate());
        //         Log::info("📩 Nội dung:\n" . $message->getTextBody());
        //         Log::info("---------------------------------------");
        //     }
        // } catch (\\Exception $e) {
        //     Log::error('Error connecting: ' . $e->getMessage());
        // }
        $this->fetchEmails();
    }

    public function fetchEmails()
    {
        try {
            DB::beginTransaction(); // Bắt đầu transaction

            $client = Client::make([
                'host' => env('IMAP_HOST'),
                'port' => env('IMAP_PORT'),
                'encryption' => env('IMAP_ENCRYPTION'),
                'validate_cert' => true,
                'username' => env('IMAP_USERNAME'),
                'password' => env('IMAP_PASSWORD'),
                'protocol' => 'imap'
            ]);

            $client->connect();
            $inbox = $client->getFolder('INBOX');

            $messages = $inbox->query()->unseen()->get();

            foreach ($messages as $message) {
                try {
                    $mail = new Mail();
                    $mail->sender_user_id = User::where('email', $message->getFrom()[0]->mail)->value('id') ?? null;
                    $mail->recipient_user_id = User::where('email', $message->getTo()[0]->mail)->value('id') ?? null;
                    $mail->subject = $message->getSubject();
                    $mail->body = $message->getTextBody();
                    $mail->received_at = $message->getDate();
                    $mail->message_id = $message->getMessageId();
                    $mail->status = 'unread';

                    // Xác định parent email & conversation ID
                    $in_reply_to = $message->getInReplyTo();
                    if ($in_reply_to) {
                        $parent_email = Mail::where('message_id', $in_reply_to)->first();
                        if ($parent_email) {
                            $mail->parent_email_id = $parent_email->id;
                            $mail->conversation_id = $parent_email->conversation_id;
                        }
                    }

                    if (!$mail->conversation_id) {
                        $mail->conversation_id = uniqid();
                    }

                    $mail->save(); // Lưu bản ghi vào DB

                    $message->setFlag('Seen'); // Đánh dấu email đã đọc

                    Log::info("Email đã được lưu thành công: {$mail->message_id}");
                } catch (\Exception $e) {
                    Log::error("Lỗi khi lưu email: " . $e->getMessage());
                }
            }

            DB::commit(); // Xác nhận transaction
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback nếu có lỗi
            Log::error("Lỗi khi fetch emails: " . $e->getMessage());
        }

        $this->emails = Mail::latest()->get();
    }

    public function markAsRead($emailId)
    {
        try {
            DB::beginTransaction();

            $email = Mail::find($emailId);
            if ($email) {
                $email->update(['status' => 'read']);
                Log::info("Email {$email->id} đã được đánh dấu là đã đọc.");
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Lỗi khi đánh dấu email là đã đọc: " . $e->getMessage());
        }

        $this->emails = Mail::latest()->get();
    }


    public function render()
    {
        return view('livewire.mailbox');
    }
}
