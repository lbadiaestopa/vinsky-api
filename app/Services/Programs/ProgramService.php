<?php

namespace App\Services\Programs;

use App\Models\Orchestra;
use App\Models\Program;
use Illuminate\Support\Arr;

class ProgramService
{
    public function create(array $data, Orchestra $orchestra): Program
    {
        return Program::create([
            'name' => $data['name'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'orchestra_id' => $orchestra->id,
        ]);
    }
}
