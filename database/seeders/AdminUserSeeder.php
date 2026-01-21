<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if admin already exists
        $admin = User::where('email', 'admin@vericrowd.local')->first();

        if (!$admin) {
            User::create([
                'name' => 'Administrator',
                'email' => 'admin@vericrowd.local',
                'password' => Hash::make('password'),
                'is_admin' => true,
                'email_verified_at' => now(),
            ]);

            $this->command->info('Admin user created: admin@vericrowd.local (password: password)');
            $this->command->warn('Please change this password immediately in production!');
        } else {
            $this->command->info('Admin user already exists');
        }
    }
}
