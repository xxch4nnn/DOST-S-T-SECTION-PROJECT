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
        $groups = [
            ['name' => 'Bachelor of Science in Computer Science, major in Data Science', 'abbreviation' => 'BS CS-DS', 'is_available' => true],
            ['name' => 'Bachelor of Science in Information Technology, major in Cybersecurity', 'abbreviation' => 'BS IT-Cyber', 'is_available' => true],
            ['name' => 'Bachelor of Science in Information Technology, major in Business Technology Management', 'abbreviation' => 'BS IT-BTM', 'is_available' => true],
            ['name' => 'Bachelor of Library in Information Science', 'abbreviation' => 'BLIS', 'is_available' => true],
            ['name' => 'Bachelor of Science in Electronics Engineering', 'abbreviation' => 'BS EE', 'is_available' => true],
        ];

        foreach ($groups as $group) {
            Course::firstOrCreate($group);
        }
    }
}
