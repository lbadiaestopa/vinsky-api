<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    /** @use HasFactory<\Database\Factories\ProgramFactory> */
    use HasFactory;

    protected $fillable = ['name', 'start_date', 'end_date', 'orchestra_id'];

    public function orchestra()
    {
        return $this->belongsTo(Orchestra::class);
    }

    public function scores()
    {
        return $this->hasMany(Score::class);
    }
}
