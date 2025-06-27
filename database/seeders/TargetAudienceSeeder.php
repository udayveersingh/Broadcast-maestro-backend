<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TargetAudience;
use App\Models\User;

class TargetAudienceSeeder extends Seeder
{
    public function run(): void
    {
        // Assuming at least one user exists
        $user = User::first();

        if (!$user) {
            $user = User::factory()->create(); // Or create manually
        }

        $audiences = [
            'Office staff',
            'Field staff',
            'Technical staff',
            'Stakeholders',
            'Regio west',
            'Regio oost',
        ];

        foreach ($audiences as $name) {
            TargetAudience::firstOrCreate([
                'user_id' => $user->id,
                'name' => $name,
                'description' => $name . ' group',
                'criteria' => json_encode([]),
            ]);
        }
    }
}

