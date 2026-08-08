<?php

namespace Database\Seeders;

use App\Models\ScholarshipType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ScholarshipTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $groups = [
            ['name'=>'DOST-SEI Undergraduate Scholarship',  'is_available'=>true],
            ['name'=>'Junior Level Science Scholarship', 'is_available'=>true],
        ];

        foreach($groups as $group){
            ScholarshipType::firstOrCreate($group);
        }
    }
}
