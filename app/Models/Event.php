<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    /** @use HasFactory<\Database\Factories\EventFactory> */
    use HasFactory;

    protected $fillable = ['repertoire', 'type', 'location', 'start_date', 'end_date', 'program_id'];

    public function program()
    {
        return $this->belongsTo(Program::class);
    }
}
