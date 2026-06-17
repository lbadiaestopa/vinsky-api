<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Membership;
use App\Models\User;
use App\Models\Orchestra;
use Carbon\Carbon;

class MembershipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('email', 'beethoven@admin.com')->first();
        $member1 = User::where('email', 'mozart@member.com')->first();
        $member2 = User::where('email', 'bach@member.com')->first();

        $obc = Orchestra::where('name', 'Orquestra Simfonica de Barcelona i Nacional de Catalunya')->first();
        $berphil = Orchestra::where('name', 'Berliner Philharmoniker')->first();
        $laphil = Orchestra::where('name', 'Los Angeles Philharmonic')->first();

        Membership::create([
            'user_id' => $admin->id,
            'orchestra_id' => $obc->id,
            'role' => 'admin',
            'member_type' => 'core',
            'instrument' => 'Violin',
            'section' => 'violin_1',
            'joined_at' => Carbon::now()->subYears(3),
        ]);

        Membership::create([
            'user_id' => $member1->id,
            'orchestra_id' => $berphil->id,
            'role' => 'member',
            'member_type' => 'substitute',
            'instrument' => 'Flute',
            'section' => 'flute',
            'joined_at' => Carbon::now()->subYear(),
        ]);

        Membership::create([
            'user_id' => $member2->id,
            'orchestra_id' => $laphil->id,
            'role' => 'member',
            'member_type' => 'guest',
            'instrument' => 'Cello',
            'section' => 'cello',
            'joined_at' => Carbon::now()->subMonths(6),
        ]);

        Membership::create([
            'user_id' => $member1->id,
            'orchestra_id' => $obc->id,
            'role' => 'member',
            'member_type' => 'guest',
            'instrument' => 'Clarinet',
            'section' => 'clarinet',
            'joined_at' => Carbon::now()->subMonths(2),
        ]);
    }
}
