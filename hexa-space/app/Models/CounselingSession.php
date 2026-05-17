<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CounselingSession extends Model
{
    protected $fillable = [
        'user_id',
        'counseling_service_id',
        'title',
        'status',
        'final_mood',
        'is_escalated',
        'doctor_notes',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function counselingService()
    {
        return $this->belongsTo(CounselingService::class);
    }
    
    public function chatMessages()
    {
        return $this->hasMany(ChatMessage::class);
    }
}
