<?php

namespace Database\Seeders;

use App\Models\Claim;
use App\Models\Evidence;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create sample users
        $user1 = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
        ]);

        $user2 = User::create([
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'password' => bcrypt('password'),
        ]);

        // Create sample claims
        $claim1 = Claim::create([
            'text' => 'The Earth is warming due to human activities.',
            'normalized_text' => 'the earth is warming due to human activities.',
            'context_url' => 'https://climate.nasa.gov/',
            'created_by' => $user1->id,
            'verdict' => 'UNVERIFIED',
            'confidence' => 0,
        ]);

        $claim2 = Claim::create([
            'text' => 'Vaccines have microchips in them.',
            'normalized_text' => 'vaccines have microchips in them.',
            'created_by' => $user2->id,
            'verdict' => 'UNVERIFIED',
            'confidence' => 0,
        ]);

        // Create sample evidence
        Evidence::create([
            'claim_id' => $claim1->id,
            'url' => 'https://climate.nasa.gov/scientific-consensus/',
            'title' => 'NASA: Scientific Consensus',
            'publisher_domain' => 'climate.nasa.gov',
            'published_at' => now()->subMonths(6),
            'stance' => 'SUPPORTS',
            'excerpt' => '97% of actively publishing climate scientists agree that climate-warming trends over the past century are very likely due to human activities.',
            'status' => 'READY',
            'created_by' => $user1->id,
        ]);

        Evidence::create([
            'claim_id' => $claim1->id,
            'url' => 'https://www.ipcc.ch/',
            'title' => 'IPCC Assessment Report',
            'publisher_domain' => 'ipcc.ch',
            'published_at' => now()->subMonths(3),
            'stance' => 'SUPPORTS',
            'excerpt' => 'It is unequivocal that human influence has warmed the climate system.',
            'status' => 'READY',
            'created_by' => $user2->id,
        ]);

        Evidence::create([
            'claim_id' => $claim2->id,
            'url' => 'https://www.cdc.gov/coronavirus/2019-ncov/vaccines/safety/fda-approval.html',
            'title' => 'CDC: Vaccine Safety and Effectiveness',
            'publisher_domain' => 'cdc.gov',
            'stance' => 'REFUTES',
            'excerpt' => 'FDA-authorized vaccines underwent the most intensive safety monitoring in U.S. history. No evidence supports claims that vaccines contain microchips.',
            'status' => 'READY',
            'created_by' => $user1->id,
        ]);
    }
}
