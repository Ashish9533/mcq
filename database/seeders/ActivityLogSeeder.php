<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class ActivityLogSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        // Get all user IDs
        $userIds = User::pluck('id')->toArray();

        // Define possible actions
        $actions = [
            'login',
            'logout',
            'exam_started',
            'exam_submitted',
            'question_answered',
            'question_reviewed',
            'profile_updated',
            'password_changed'
        ];

        // Create 100 activity logs
        for ($i = 0; $i < 100; $i++) {
            $action = $faker->randomElement($actions);
            $userId = $faker->randomElement($userIds);
            
            // Generate appropriate details based on action
            $details = $this->generateDetails($action, $faker);

            ActivityLog::create([
                'user_id' => $userId,
                'action' => $action,
                'details' => $details,
                'ip_address' => $faker->ipv4,
                'created_at' => $faker->dateTimeBetween('-1 year', 'now'),
                'updated_at' => $faker->dateTimeBetween('-1 year', 'now'),
            ]);
        }
    }

    private function generateDetails($action, $faker)
    {
        switch ($action) {
            case 'login':
                return 'User logged in successfully';
            case 'logout':
                return 'User logged out';
            case 'exam_started':
                return 'Started Software Development MCQ exam';
            case 'exam_submitted':
                return 'Submitted Software Development MCQ exam';
            case 'question_answered':
                return 'Answered question #' . $faker->numberBetween(1, 50);
            case 'question_reviewed':
                return 'Marked question #' . $faker->numberBetween(1, 50) . ' for review';
            case 'profile_updated':
                return 'Updated profile information';
            case 'password_changed':
                return 'Changed account password';
            default:
                return $faker->sentence;
        }
    }
} 