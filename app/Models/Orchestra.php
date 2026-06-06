<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Orchestra extends Model
{
    use HasFactory;
    
    protected $fillable = ['name', 'location'];

    public function memberships()
    {
        return $this->hasMany(Membership::class);
    }
}
