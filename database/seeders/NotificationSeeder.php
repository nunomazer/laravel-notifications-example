<?php

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            // Old read
            Notification::factory()
                ->count(rand(10, 20))
                ->create([
                    'user_id' => $user->id,
                    'created_at' => fake()->dateTimeBetween('-3 months', '-1 week'),
                    'read_at' => fake()->dateTimeBetween('-2 months', '-1 week'),
                ]);

            // New read and not read
            Notification::factory()
                ->count(rand(5, 15))
                ->create([
                    'user_id' => $user->id,
                    'created_at' => fake()->dateTimeBetween('-1 week', 'now'),
                    'read_at' => fake()->optional(0.4)->dateTimeBetween('-1 week', 'now'),
                ]);

            // Today mainly read
            Notification::factory()
                ->count(rand(2, 8))
                ->create([
                    'user_id' => $user->id,
                    'created_at' => fake()->dateTimeBetween('today', 'now'),
                    'read_at' => fake()->optional(0.2)->dateTimeBetween('today', 'now'),
                ]);
        }

        $totalNotifications = Notification::count();
    }
}