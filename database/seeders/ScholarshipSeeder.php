<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ScholarshipSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['name' => 'RA 7687 (DOST-SEI S&T Scholarship)', 'is_available' => true],
            ['name' => 'RA 10612 (DOST-SEI Merit Scholarship)', 'is_available' => true],
            ['name' => 'Merit Scholarship', 'is_available' => true],
        ];

        foreach ($data as $item) {
            DB::table('scholarships')->updateOrInsert(['name' => $item['name']], $item);
        }
    }
}
