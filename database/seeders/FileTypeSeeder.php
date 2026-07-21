<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FileTypeSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['name' => 'O.R. / Official Receipt', 'year' => '2026'],
            ['name' => 'Clearance Form', 'year' => '2026'],
            ['name' => 'Contract', 'year' => '2026'],
            ['name' => 'Grades', 'year' => '2026'],
        ];

        foreach ($data as $item) {
            DB::table('file_types')->updateOrInsert(['name' => $item['name']], $item);
        }
    }
}
