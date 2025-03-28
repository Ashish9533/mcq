<?php

namespace Database\Seeders;

use App\Models\Question;
use App\Models\Category;
use Illuminate\Database\Seeder;

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $questions = [
            // DSA Questions (30 questions)
            [
                'category' => 'dsa',
                'questions' => [
                    [
                        'question_text' => 'What is the time complexity of binary search?',
                        'explanation' => 'Binary search has a time complexity of O(log n) as it divides the search space in half with each iteration.',
                        'difficulty_level' => 'medium',
                        'is_active' => true,
                        'order' => 1,
                        'options' => [
                            ['option_text' => 'O(n)', 'is_correct' => false, 'order' => 1],
                            ['option_text' => 'O(log n)', 'is_correct' => true, 'order' => 2],
                            ['option_text' => 'O(n²)', 'is_correct' => false, 'order' => 3],
                            ['option_text' => 'O(1)', 'is_correct' => false, 'order' => 4]
                        ]
                    ],
                    [
                        'question_text' => 'Which data structure uses LIFO principle?',
                        'explanation' => 'Stack follows Last In First Out (LIFO) principle where the last element added is the first one to be removed.',
                        'difficulty_level' => 'easy',
                        'is_active' => true,
                        'order' => 2,
                        'options' => [
                            ['option_text' => 'Queue', 'is_correct' => false, 'order' => 1],
                            ['option_text' => 'Stack', 'is_correct' => true, 'order' => 2],
                            ['option_text' => 'Array', 'is_correct' => false, 'order' => 3],
                            ['option_text' => 'Linked List', 'is_correct' => false, 'order' => 4]
                        ]
                    ],
                    // Add 28 more DSA questions here...
                ]
            ],
            // System Design Questions (30 questions)
            [
                'category' => 'system-design',
                'questions' => [
                    [
                        'question_text' => 'What is the CAP theorem?',
                        'explanation' => 'The CAP theorem states that a distributed database system can only guarantee two out of three properties: Consistency, Availability, and Partition tolerance.',
                        'difficulty_level' => 'hard',
                        'is_active' => true,
                        'order' => 1,
                        'options' => [
                            ['option_text' => 'A theorem about CPU architecture', 'is_correct' => false, 'order' => 1],
                            ['option_text' => 'A theorem about distributed systems', 'is_correct' => true, 'order' => 2],
                            ['option_text' => 'A theorem about memory management', 'is_correct' => false, 'order' => 3],
                            ['option_text' => 'A theorem about network protocols', 'is_correct' => false, 'order' => 4]
                        ]
                    ],
                    // Add 29 more System Design questions here...
                ]
            ],
            // Microservices Questions (30 questions)
            [
                'category' => 'microservices',
                'questions' => [
                    [
                        'question_text' => 'What is service discovery?',
                        'explanation' => 'Service discovery is the process of automatically detecting and registering services in a distributed system.',
                        'difficulty_level' => 'medium',
                        'is_active' => true,
                        'order' => 1,
                        'options' => [
                            ['option_text' => 'Finding lost services', 'is_correct' => false, 'order' => 1],
                            ['option_text' => 'Automatically detecting services', 'is_correct' => true, 'order' => 2],
                            ['option_text' => 'Creating new services', 'is_correct' => false, 'order' => 3],
                            ['option_text' => 'Deleting old services', 'is_correct' => false, 'order' => 4]
                        ]
                    ],
                    // Add 29 more Microservices questions here...
                ]
            ],
            // Database Design Questions (30 questions)
            [
                'category' => 'database',
                'questions' => [
                    [
                        'question_text' => 'What is normalization?',
                        'explanation' => 'Normalization is the process of organizing data to reduce redundancy and improve data integrity.',
                        'difficulty_level' => 'medium',
                        'is_active' => true,
                        'order' => 1,
                        'options' => [
                            ['option_text' => 'Making data bigger', 'is_correct' => false, 'order' => 1],
                            ['option_text' => 'Organizing data to reduce redundancy', 'is_correct' => true, 'order' => 2],
                            ['option_text' => 'Making data smaller', 'is_correct' => false, 'order' => 3],
                            ['option_text' => 'Making data faster', 'is_correct' => false, 'order' => 4]
                        ]
                    ],
                    // Add 29 more Database Design questions here...
                ]
            ],
            // Web Development Questions (30 questions)
            [
                'category' => 'web-dev',
                'questions' => [
                    [
                        'question_text' => 'What is the purpose of CSS Grid?',
                        'explanation' => 'CSS Grid is a two-dimensional layout system that allows you to create complex web layouts with rows and columns.',
                        'difficulty_level' => 'medium',
                        'is_active' => true,
                        'order' => 1,
                        'options' => [
                            ['option_text' => 'To style text', 'is_correct' => false, 'order' => 1],
                            ['option_text' => 'To create 2D layouts', 'is_correct' => true, 'order' => 2],
                            ['option_text' => 'To handle animations', 'is_correct' => false, 'order' => 3],
                            ['option_text' => 'To process forms', 'is_correct' => false, 'order' => 4]
                        ]
                    ],
                    // Add 29 more Web Development questions here...
                ]
            ],
            // Mobile Development Questions (30 questions)
            [
                'category' => 'mobile-dev',
                'questions' => [
                    [
                        'question_text' => 'What is React Native?',
                        'explanation' => 'React Native is a framework for building native mobile applications using React and JavaScript.',
                        'difficulty_level' => 'medium',
                        'is_active' => true,
                        'order' => 1,
                        'options' => [
                            ['option_text' => 'A web browser', 'is_correct' => false, 'order' => 1],
                            ['option_text' => 'A mobile app framework', 'is_correct' => true, 'order' => 2],
                            ['option_text' => 'A database system', 'is_correct' => false, 'order' => 3],
                            ['option_text' => 'A server framework', 'is_correct' => false, 'order' => 4]
                        ]
                    ],
                    // Add 29 more Mobile Development questions here...
                ]
            ],
            // DevOps Questions (30 questions)
            [
                'category' => 'devops',
                'questions' => [
                    [
                        'question_text' => 'What is Docker?',
                        'explanation' => 'Docker is a platform for developing, shipping, and running applications in containers.',
                        'difficulty_level' => 'medium',
                        'is_active' => true,
                        'order' => 1,
                        'options' => [
                            ['option_text' => 'A programming language', 'is_correct' => false, 'order' => 1],
                            ['option_text' => 'A container platform', 'is_correct' => true, 'order' => 2],
                            ['option_text' => 'A database system', 'is_correct' => false, 'order' => 3],
                            ['option_text' => 'A web server', 'is_correct' => false, 'order' => 4]
                        ]
                    ],
                    // Add 29 more DevOps questions here...
                ]
            ],
            // Security Questions (30 questions)
            [
                'category' => 'security',
                'questions' => [
                    [
                        'question_text' => 'What is SQL Injection?',
                        'explanation' => 'SQL Injection is a security vulnerability that allows attackers to manipulate database queries through malicious input.',
                        'difficulty_level' => 'medium',
                        'is_active' => true,
                        'order' => 1,
                        'options' => [
                            ['option_text' => 'A database backup method', 'is_correct' => false, 'order' => 1],
                            ['option_text' => 'A security vulnerability', 'is_correct' => true, 'order' => 2],
                            ['option_text' => 'A data encryption technique', 'is_correct' => false, 'order' => 3],
                            ['option_text' => 'A backup recovery method', 'is_correct' => false, 'order' => 4]
                        ]
                    ],
                    // Add 29 more Security questions here...
                ]
            ],
            // Cloud Computing Questions (30 questions)
            [
                'category' => 'cloud',
                'questions' => [
                    [
                        'question_text' => 'What is AWS Lambda?',
                        'explanation' => 'AWS Lambda is a serverless compute service that runs code in response to events.',
                        'difficulty_level' => 'medium',
                        'is_active' => true,
                        'order' => 1,
                        'options' => [
                            ['option_text' => 'A database service', 'is_correct' => false, 'order' => 1],
                            ['option_text' => 'A serverless compute service', 'is_correct' => true, 'order' => 2],
                            ['option_text' => 'A storage service', 'is_correct' => false, 'order' => 3],
                            ['option_text' => 'A networking service', 'is_correct' => false, 'order' => 4]
                        ]
                    ],
                    // Add 29 more Cloud Computing questions here...
                ]
            ],
            // Testing Questions (30 questions)
            [
                'category' => 'testing',
                'questions' => [
                    [
                        'question_text' => 'What is Unit Testing?',
                        'explanation' => 'Unit testing is a software testing method where individual components of a software are tested in isolation.',
                        'difficulty_level' => 'medium',
                        'is_active' => true,
                        'order' => 1,
                        'options' => [
                            ['option_text' => 'Testing the entire system', 'is_correct' => false, 'order' => 1],
                            ['option_text' => 'Testing individual components', 'is_correct' => true, 'order' => 2],
                            ['option_text' => 'Testing user interface', 'is_correct' => false, 'order' => 3],
                            ['option_text' => 'Testing network performance', 'is_correct' => false, 'order' => 4]
                        ]
                    ],
                    // Add 29 more Testing questions here...
                ]
            ]
        ];

        foreach ($questions as $categoryQuestions) {
            $category = Category::where('slug', $categoryQuestions['category'])->first();
            
            foreach ($categoryQuestions['questions'] as $questionData) {
                $options = $questionData['options'];
                unset($questionData['options']);
                
                $question = Question::create([
                    'category_id' => $category->id,
                    ...$questionData
                ]);

                foreach ($options as $option) {
                    $question->options()->create($option);
                }
            }
        }
    }
} 