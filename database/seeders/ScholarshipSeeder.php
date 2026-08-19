<?php

namespace Database\Seeders;

use App\Models\Scholarship;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ScholarshipSeeder extends Seeder
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
            Scholarship::firstOrCreate($group);
        }
    }
}
