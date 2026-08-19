<?php

namespace Database\Seeders;

use App\Models\ClearanceStatus;
use Illuminate\Database\Seeder;

class ClearanceStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $groups = [
            ['name' => 'Not Cleared', 'is_available' => true],
            ['name' => 'Cleared', 'is_available' => true],
        ];

        foreach ($groups as $group) {
            ClearanceStatus::firstOrCreate($group);
        }
    }
}
