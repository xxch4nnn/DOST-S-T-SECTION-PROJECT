<?php

namespace Database\Seeders;

use App\Models\School;
use Illuminate\Database\Seeder;

class SchoolSeeder extends Seeder
{
    public function run(): void
    {
        School::firstOrCreate(['name' => 'University of the Philippines Diliman', 'campus' => 'Diliman', 'is_available' => true]);
        School::firstOrCreate(['name' => 'Ateneo de Manila University', 'campus' => 'Loyola Heights', 'is_available' => true]);
        School::firstOrCreate(['name' => 'De La Salle University', 'campus' => 'Taft', 'is_available' => true]);
        School::firstOrCreate(['name' => 'University of Santo Tomas', 'campus' => 'Sampaloc', 'is_available' => true]);
    }
}
