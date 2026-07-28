<?php

namespace Database\Seeders;

use App\Models\Region;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $groups = [
            ['name'=>'Region I - Ilocos Region', 'abbreviation'=>'Region I', 'is_available'=>true],
            ['name'=>'Region II - Cagayan Valley', 'abbreviation'=>'Region II', 'is_available'=>true],
            ['name'=>'Region III - Central Luzon', 'abbreviation'=>'Region III', 'is_available'=>true],
            ['name'=>'Region IV-A - CALABARZON', 'abbreviation'=>'Region IV-A', 'is_available'=>true],
            ['name'=>'Region IV-B - MIMAROPA', 'abbreviation'=>'Region IV-B', 'is_available'=>true],
            ['name'=>'Region V - Bicol Region', 'abbreviation'=>'Region V', 'is_available'=>true],
            ['name'=>'Region VI - Western Visayas', 'abbreviation'=>'Region VI', 'is_available'=>true],
            ['name'=>'Region VII - Central Visayas', 'abbreviation'=>'Region VII', 'is_available'=>true],
            ['name'=>'Region VIII - Eastern Visayas', 'abbreviation'=>'Region VIII', 'is_available'=>true],
            ['name'=>'Region IX - Zamboanga Peninsula', 'abbreviation'=>'Region IX', 'is_available'=>true],
            ['name'=>'Region X - Northern Mindanao', 'abbreviation'=>'Region X', 'is_available'=>true],
            ['name'=>'Region XI - Davao Region', 'abbreviation'=>'Region XI', 'is_available'=>true],
            ['name'=>'Region XII - SOCCKSARGEN', 'abbreviation'=>'Region XII', 'is_available'=>true],
            ['name'=>'Region XIII - CARAGA', 'abbreviation'=>'Region XIII', 'is_available'=>true],
            ['name'=>'NCR - National Capital Region', 'abbreviation'=>'NCR', 'is_available'=>true],
            ['name'=>'CAR - Cordillera Administrative Region', 'abbreviation'=>'CAR', 'is_available'=>true],
            ['name'=>'BARMM - Bangsamoro Autonomous Region in Muslim Mindanao', 'abbreviation'=>'BARMM', 'is_available'=>true]
        ];

        foreach($groups as $group){
            // firstOrCreate ensures we don't accidentally insert duplicates 
            // if the seeder is run multiple times.
            Region::firstOrCreate($group);
        }
    }
}
