<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Orchestra;
use App\Models\Program;

class ProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $obc = Orchestra::where('name', 'Orquestra Simfonica de Barcelona i Nacional de Catalunya')->first();
        $berphil = Orchestra::where('name', 'Berliner Philharmoniker')->first();
        $laphil = Orchestra::where('name', 'Los Angeles Philharmonic')->first();

        Program::create([
            'orchestra_id' => $obc->id,
            'name' => 'Beethoven Cycle 2026',
            'start_date' => '2026-01-15',
            'end_date' => '2026-06-30',
        ]);

        Program::create([
            'orchestra_id' => $obc->id,
            'name' => 'Romantic Masters',
            'start_date' => '2026-09-01',
            'end_date' => '2026-12-20',
        ]);

        Program::create([
            'orchestra_id' => $berphil->id,
            'name' => 'Young Talent Showcase',
            'start_date' => '2026-02-01',
            'end_date' => '2026-05-31',
        ]);

        Program::create([
            'orchestra_id' => $laphil->id,
            'name' => 'Mediterranean Classics',
            'start_date' => '2026-03-01',
            'end_date' => '2026-07-15',
        ]);
    }
}
