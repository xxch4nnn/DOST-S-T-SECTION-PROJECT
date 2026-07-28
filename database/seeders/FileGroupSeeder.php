<?php

namespace Database\Seeders;

use App\Models\FileGroup;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FileGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $groups = [
            ['name' => 'Scholarly Documents', 
                'slug'=> 'scholarly_documents'],
            ['name' => 'Administrative Records',
                'slug'=>'administrative_records'],
            ['name' => 'Financial Reports',
                'slug'=>'financial_reports']
        ];

        foreach ($groups as $group) {
            // firstOrCreate ensures we don't accidentally insert duplicates 
            // if the seeder is run multiple times.
            FileGroup::firstOrCreate($group);
        }
    }
}
