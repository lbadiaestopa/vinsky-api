<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Event;
use App\Models\Program;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $beethoven = Program::where('name', 'Beethoven Cycle 2026')->first();
        $romantic = Program::where('name', 'Romantic Masters')->first();
        $youngTalent = Program::where('name', 'Young Talent Showcase')->first();
        $mediterranean = Program::where('name', 'Mediterranean Classics')->first();

        Event::create([
            'program_id' => $beethoven->id,
            'repertoire' => 'Beethoven Symphony No. 5',
            'type' => 'concert',
            'location' => 'L’Auditori',
            'start_date' => '2026-02-15 19:00:00',
            'end_date' => '2026-02-15 21:00:00',
        ]);

        Event::create([
            'program_id' => $beethoven->id,
            'repertoire' => 'Beethoven Symphony No. 9',
            'type' => 'concert',
            'location' => 'Palau de la Música Catalana',
            'start_date' => '2026-05-20 19:30:00',
            'end_date' => '2026-05-20 22:00:00',
        ]);

        Event::create([
            'program_id' => $romantic->id,
            'repertoire' => 'Tchaikovsky and Brahms',
            'type' => 'rehearsal',
            'location' => 'L’Auditori',
            'start_date' => '2026-10-10 10:00:00',
            'end_date' => '2026-10-10 13:00:00',
        ]);

        Event::create([
            'program_id' => $youngTalent->id,
            'repertoire' => 'Young Soloists Gala',
            'type' => 'concert',
            'location' => 'Berliner Philharmonie',
            'start_date' => '2026-04-18 18:00:00',
            'end_date' => '2026-04-18 20:30:00',
        ]);

        Event::create([
            'program_id' => $mediterranean->id,
            'repertoire' => 'Mozart and Haydn',
            'type' => 'soundcheck',
            'location' => 'Hollywood Bowl',
            'start_date' => '2026-06-12 20:00:00',
            'end_date' => '2026-06-12 22:00:00',
        ]);
    }
}
