<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        Course::firstOrCreate(['name' => 'Bachelor of Science in Computer Science', 'abbreviation' => 'BSCS', 'is_available' => true]);
        Course::firstOrCreate(['name' => 'Bachelor of Science in Information Technology', 'abbreviation' => 'BSIT', 'is_available' => true]);
        Course::firstOrCreate(['name' => 'Bachelor of Science in Electronics Engineering', 'abbreviation' => 'BSECE', 'is_available' => true]);
        Course::firstOrCreate(['name' => 'Bachelor of Science in Accountancy', 'abbreviation' => 'BSA', 'is_available' => true]);
    }
}
