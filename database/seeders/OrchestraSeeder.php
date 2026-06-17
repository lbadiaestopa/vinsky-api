<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Orchestra;

class OrchestraSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Orchestra::create([
            'name' => 'Orquestra Simfonica de Barcelona i Nacional de Catalunya',
            'location' => 'Barcelona',
        ]);

        Orchestra::create([
            'name' => 'Berliner Philharmoniker',
            'location' => 'Berlin',
        ]);

        Orchestra::create([
            'name' => 'Los Angeles Philharmonic',
            'location' => 'Loa Angeles',
        ]);
    }
}
