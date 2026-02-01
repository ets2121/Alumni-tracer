<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $courses = [
            // Undergraduate
            ['code' => 'BSIT', 'name' => 'Bachelor of Science in Information Technology', 'category' => 'Undergraduate', 'description' => 'Computing, Software development, and IT infrastructure management.'],
            ['code' => 'BSCS', 'name' => 'Bachelor of Science in Computer Science', 'category' => 'Undergraduate', 'description' => 'Algorithms, computational theory, and advanced software engineering.'],
            ['code' => 'BSCE', 'name' => 'Bachelor of Science in Civil Engineering', 'category' => 'Undergraduate', 'description' => 'Infrastructure design, construction, and environmental management.'],
            ['code' => 'BSBA', 'name' => 'Bachelor of Science in Business Administration', 'category' => 'Undergraduate', 'description' => 'Corporate management, marketing, and financial operations.'],

            // Graduate
            ['code' => 'MIT', 'name' => 'Master of Information Technology', 'category' => 'Graduate', 'description' => 'Advanced IT management and specialized technical research.'],
            ['code' => 'MBA', 'name' => 'Master of Business Administration', 'category' => 'Graduate', 'description' => 'Leadership, strategic management, and global business concepts.'],

            // Certificate
            ['code' => 'CCS', 'name' => 'Certificate in Computer Studies', 'category' => 'Certificate', 'description' => 'Foundational computing skills for rapid entry into tech roles.'],
            ['code' => 'CCNA', 'name' => 'Cisco Certified Network Associate', 'category' => 'Certificate', 'description' => 'Networking fundamentals and Cisco equipment configuration.'],
        ];

        foreach ($courses as $course) {
            Course::updateOrCreate(['code' => $course['code']], $course);
        }
    }
}
