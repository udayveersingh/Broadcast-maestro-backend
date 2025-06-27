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
            ['name' => 'Raise awareness'],
            ['name' => 'Influence behavior'],
        ];

        foreach ($goals as $goal) {
            Goal::firstOrCreate($goal);
        }
    }
}

