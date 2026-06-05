<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Orchestra extends Model
{
    protected $fillable = ['name', 'location'];

    public function memberships()
    {
        return $this->hasMany(Membership::class);
    }
}
