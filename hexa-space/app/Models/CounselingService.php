<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CounselingService extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function counselingSessions()
    {
        return $this->hasMany(CounselingSession::class);
    }
}
