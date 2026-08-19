<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

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
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $testAdmin->assignRole('Super Admin');

        $adminUser = User::firstOrCreate([
            'email' => 'a@a',
        ], [
            'name' => 'admin',
            'password' => Hash::make('admin'),
            'email_verified_at' => now(),
        ]);
        $adminUser->assignRole('Super Admin');

        $this->call([
            FileGroupSeeder::class,
            RegionSeeder::class,
            ScholarshipSeeder::class,
            ScholarshipTypeSeeder::class,
            SchoolSeeder::class,
            CourseSeeder::class,
            ClearanceStatusSeeder::class,
            UserSeeder::class,
            ScholarSeeder::class,
            FileTypeSeeder::class,
            DocumentSeeder::class,
        ]);
    }
}
