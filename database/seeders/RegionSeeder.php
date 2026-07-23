<?php

namespace Database\Seeders;

use App\Models\Region;
use Illuminate\Database\Seeder;

class RegionSeeder extends Seeder
{
    public function run(): void
    {
        Region::firstOrCreate(['name' => 'National Capital Region', 'abbreviation' => 'NCR', 'is_available' => true]);
        Region::firstOrCreate(['name' => 'Region I - Ilocos', 'abbreviation' => 'REG I', 'is_available' => true]);
        Region::firstOrCreate(['name' => 'Region II - Cagayan Valley', 'abbreviation' => 'REG II', 'is_available' => true]);
        Region::firstOrCreate(['name' => 'Region III - Central Luzon', 'abbreviation' => 'REG III', 'is_available' => true]);
    }
}
