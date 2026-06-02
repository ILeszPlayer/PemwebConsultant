<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MessageFeedback extends Model
{
    protected $fillable = [
        'chat_message_id',
        'user_id',
        'feedback',
    ];

    public function chatMessage()
    {
        return $this->belongsTo(ChatMessage::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
