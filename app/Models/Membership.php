<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Membership extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'orchestra_id',
        'role',
        'member_type',
        'instrument',
        'section',
        'joined_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orchestra()
    {
        return $this->belongsTo(Orchestra::class);
    }
}
