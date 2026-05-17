<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    protected $fillable = [
        'user_id',
        'sender',
        'message',
        'counseling_session_id',
    ];
    
    public function counselingSession()
    {
        return $this->belongsTo(CounselingSession::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
