<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FileTypeSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['name' => 'Scholarship Agreement', 'year' => '2026'],
            ['name' => 'Amendatory Agreement', 'year' => '2026'],
            ['name' => 'Report of Grades', 'year' => '2026'],
            ['name' => 'Certificate of Grades (COG)', 'year' => '2026'],
            ['name' => 'Transcript of Records (TOR)', 'year' => '2026'],
            ['name' => 'Certificate of Graduation / Diploma', 'year' => '2026'],
            ['name' => 'Enrollment / Registration Form', 'year' => '2026'],
            ['name' => 'Clearance Form / Certificate', 'year' => '2026'],
            ['name' => 'Official Receipt (O.R.)', 'year' => '2026'],
            ['name' => 'Medical Certificate', 'year' => '2026'],
            ['name' => 'Other Supporting Documents', 'year' => '2026'],
        ];

        foreach ($data as $item) {
            DB::table('file_types')->updateOrInsert(['name' => $item['name']], $item);
        }
    }
}
