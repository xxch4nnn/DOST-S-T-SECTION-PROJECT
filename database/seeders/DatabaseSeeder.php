<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test Admin',
            'email' => 'test@example.com',
        ]);

        $this->call([
            RolesAndPermissionsSeeder::class,
            ScholarshipSeeder::class,
            ScholarshipTypeSeeder::class,
            SchoolSeeder::class,
            CourseSeeder::class,
            RegionSeeder::class,
            ClearanceStatusSeeder::class,
            FileGroupSeeder::class,
            FileTypeSeeder::class,
        ]);
    }
}
