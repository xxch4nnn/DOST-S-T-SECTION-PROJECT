<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $groups = [
            ['name'=> 'Waks', 'email'=> 'maclangw26@gmail.com', 'password'=> 'Wakster2112'],
            ['name'=> 'Admin', 'email'=> 'admin@admin', 'password'=> 'adminadmin'],
        ];

        foreach($groups as $group){
            $group['password'] = password_hash($group['password'], PASSWORD_BCRYPT);
            User::firstOrCreate($group);
        }
    }
}
