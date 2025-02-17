<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mail extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_user_id',
        'recipient_user_id',
        'parent_email_id',
        'conversation_id',
        'subject',
        'body',
        'received_at',
        'status',
        'message_id'
    ];
}
