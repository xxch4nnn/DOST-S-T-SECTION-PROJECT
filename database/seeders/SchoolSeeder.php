<?php

namespace Database\Seeders;

use App\Models\School;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SchoolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $groups = [
            ['name'=>'Davao Doctos College, Inc.'],
            ['name'=>'Davao de Oro State College'],
            ['name'=>'Davao del Sur State College'],
            ['name'=>'Davao del Norte State College'],
            ['name'=>'Davao Oriental State University'],
            ['name'=>'Mapúa Malayan Colleges Mindanao'],
            ['name'=>'University of the Immaculate Conception'],
            ['name'=>'University of Mindanao', 'campus'=>'Matina'],
            ['name'=>'University of Mindanao', 'campus'=>'Tagum'],
            ['name'=>'University of the Philippines Mindanao', 'campus'=>'Mintal'],
            ['name'=>'University of Southeastern Philippines', 'campus'=>'Obrero'],
            ['name'=>'University of Southeastern Philippines', 'campus'=>'Tagum'],
        ];

        foreach($groups as $group){
            School::firstOrCreate($group);
        }
    }
}
