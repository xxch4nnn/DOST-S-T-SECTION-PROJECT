<?php

namespace Database\Seeders;

use App\Models\ScholarshipType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ScholarshipTypeSeeder extends Seeder
{
    public function run(): void
    {
        ScholarshipType::firstOrCreate(['name' => 'Undergraduate']);
        ScholarshipType::firstOrCreate(['name' => 'Graduate']);
        ScholarshipType::firstOrCreate(['name' => 'TES']);
        ScholarshipType::firstOrCreate(['name' => 'STA']);
    }
}
