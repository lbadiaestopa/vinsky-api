<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Ludwig',
            'last_name' => 'van Beethoven',
            'email' => 'beethoven@admin.com',
            'password' => Hash::make('password'),
        ]);

        User::create([
            'name' => 'Wolfgang Amadeus',
            'last_name' => 'Mozart',
            'email' => 'mozart@member.com',
            'password' => Hash::make('password'),
        ]);

        User::create([
            'name' => 'Johann Sebastian',
            'last_name' => 'Bach',
            'email' => 'bach@member.com',
            'password' => Hash::make('password'),
        ]);
    }
}
