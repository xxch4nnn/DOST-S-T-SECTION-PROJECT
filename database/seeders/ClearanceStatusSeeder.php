<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClearanceStatusSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['name' => 'Cleared', 'is_available' => true],
            ['name' => 'Pending', 'is_available' => true],
            ['name' => 'With Accountability', 'is_available' => true],
        ];

        foreach ($data as $item) {
            DB::table('clearance_statuses')->updateOrInsert(['name' => $item['name']], $item);
        }
    }
}
