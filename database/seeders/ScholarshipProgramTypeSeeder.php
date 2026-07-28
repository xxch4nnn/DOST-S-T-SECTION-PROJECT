<?php

namespace Database\Seeders;

use App\Models\ScholarshipProgramType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ScholarshipProgramTypeSeeder extends Seeder
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
            ScholarshipProgramType::firstOrCreate($group);
        }
    }
}
