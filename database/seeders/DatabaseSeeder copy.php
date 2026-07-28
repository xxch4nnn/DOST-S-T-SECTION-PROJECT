<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    // use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $this->call([
            FileGroupSeeder::class,
            RegionSeeder::class,
            ScholarshipProgramSeeder::class,
            ScholarshipProgramTypeSeeder::class,
            SchoolSeeder::class,
            CourseSeeder::class,
            ClearanceStatusSeeder::class,
            UserSeeder::class,
            ScholarSeeder::class,
            FileTypeSeeder::class
        ]);
        
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}


User::factory()->create([
            'name' => 'Test Admin',
            'email' => 'test@example.com',
        ]);