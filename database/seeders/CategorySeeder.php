<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Data Structures & Algorithms',
                'slug' => 'dsa',
                'description' => 'Questions about data structures, algorithms, and their implementations',
                'is_active' => true,
                'order' => 1
            ],
            [
                'name' => 'System Design',
                'slug' => 'system-design',
                'description' => 'Questions about system architecture, scalability, and design patterns',
                'is_active' => true,
                'order' => 2
            ],
            [
                'name' => 'Microservices',
                'slug' => 'microservices',
                'description' => 'Questions about microservices architecture, communication, and deployment',
                'is_active' => true,
                'order' => 3
            ],
            [
                'name' => 'Database Design',
                'slug' => 'database',
                'description' => 'Questions about database design, optimization, and management',
                'is_active' => true,
                'order' => 4
            ],
            [
                'name' => 'Web Development',
                'slug' => 'web-dev',
                'description' => 'Questions about web technologies, frameworks, and best practices',
                'is_active' => true,
                'order' => 5
            ],
            [
                'name' => 'Mobile Development',
                'slug' => 'mobile-dev',
                'description' => 'Questions about mobile app development, platforms, and frameworks',
                'is_active' => true,
                'order' => 6
            ],
            [
                'name' => 'DevOps & CI/CD',
                'slug' => 'devops',
                'description' => 'Questions about continuous integration, deployment, and operations',
                'is_active' => true,
                'order' => 7
            ],
            [
                'name' => 'Security',
                'slug' => 'security',
                'description' => 'Questions about application security, authentication, and authorization',
                'is_active' => true,
                'order' => 8
            ],
            [
                'name' => 'Cloud Computing',
                'slug' => 'cloud',
                'description' => 'Questions about cloud platforms, services, and architecture',
                'is_active' => true,
                'order' => 9
            ],
            [
                'name' => 'Testing & Quality Assurance',
                'slug' => 'testing',
                'description' => 'Questions about software testing, quality assurance, and best practices',
                'is_active' => true,
                'order' => 10
            ]
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
} 