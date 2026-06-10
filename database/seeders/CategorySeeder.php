<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'category_name' => 'Information Technology',
                'description' => 'Explore computer science, programming, web development, cybersecurity, AI, and cloud computing — the foundation of the digital world.',
            ],
            [
                'category_name' => 'Business & Entrepreneurship',
                'description' => 'Develop business acumen, marketing expertise, financial management skills, and entrepreneurial thinking.',
            ],
            [
                'category_name' => 'Education',
                'description' => 'Advance your teaching career with modern methodologies, educational technology, and classroom management strategies.',
            ],
            [
                'category_name' => 'Languages',
                'description' => 'Master new languages including English, French, Kinyarwanda, and build effective communication skills.',
            ],
            [
                'category_name' => 'Engineering',
                'description' => 'Study electrical, civil, and mechanical engineering principles with hands-on practical applications.',
            ],
            [
                'category_name' => 'Health Sciences',
                'description' => 'Learn public health, first aid, health information systems, and foundational medical knowledge.',
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
