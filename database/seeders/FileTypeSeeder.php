<?php

namespace Database\Seeders;

use App\Models\FileGroup;
use App\Models\FileType;
use Illuminate\Database\Seeder;

class FileTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
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
