<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    // use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
        ]);

        $testAdmin = User::firstOrCreate([
            'email' => 'test@example.com',
        ], [
            'name' => 'Test Admin',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);
        $testAdmin->assignRole('Super Admin');

        User::factory()->create([
            'name' => 'admin',
            'email' => 'a@a',
            'password' => 'admin',
            'email_verified_at' => now(),
        ]);

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
    }
}
