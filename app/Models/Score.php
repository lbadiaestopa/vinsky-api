<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Score extends Model
{
    /** @use HasFactory<\Database\Factories\ScoreFactory> */
    use HasFactory;

    protected $fillable = [
        'program_id',
        'title',
        'file_path',
        'original_name',
        'size',
        'mime_type'
    ];

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }
}
