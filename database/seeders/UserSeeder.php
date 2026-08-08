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
            ['name'=> 'Waks', 'email'=> 'maclangw26@gmail.com', 'password'=> 'Wakster2112', 'role' => 'Super Admin'],
            ['name'=> 'Admin', 'email'=> 'admin@admin', 'password'=> 'adminadmin', 'role' => 'Admin'],
        ];

        foreach($groups as $group){
            $roleName = $group['role'];
            unset($group['role']);
            $group['password'] = password_hash($group['password'], PASSWORD_BCRYPT);
            $user = User::firstOrCreate(['email' => $group['email']], $group);
            if ($roleName) {
                $user->assignRole($roleName);
            }
        }
    }
}
