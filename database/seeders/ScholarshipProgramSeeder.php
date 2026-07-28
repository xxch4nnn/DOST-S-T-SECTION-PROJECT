<?php

namespace Database\Seeders;

use App\Models\ScholarshipProgram;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ScholarshipProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $groups = [
            ['name'=>'DOST-SEI RA 10612',  'is_available'=>true],
            ['name'=>'DOST-SEI RA 7687', 'is_available'=>true],
            ['name'=>'DOST-SEI Merit', 'is_available'=>true]
        ];

        foreach($groups as $group){
            ScholarshipProgram::firstOrCreate($group);
        }
    }
}
