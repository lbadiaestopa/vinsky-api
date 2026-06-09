<?php

namespace App\Services\Programs;

use App\Models\Orchestra;
use App\Models\Program;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

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

    public function index(Orchestra $orchestra): LengthAwarePaginator
    {
        return Program::query()
            ->where('orchestra_id', $orchestra->id)
            ->orderBy('start_date', 'desc')
            ->paginate(15);
    }
}
