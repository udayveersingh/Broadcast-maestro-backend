<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Goal;

class GoalSeeder extends Seeder
{
    public function run(): void
    {
        $goals = [
            ['name' => 'Inform'],
            // ['name' => 'Raise awareness'],
            // ['name' => 'Influence behavior'],
            ['name' => 'Activate'],
            ['name' => 'Event'],
        ];

        foreach ($goals as $goal) {
            Goal::firstOrCreate($goal);
        }
    }
}

