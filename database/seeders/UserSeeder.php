<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'id' => 1,
            'name' => 'Admin',
            'last_name' => 'Admin',
            'maternal_surname' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('123_Admin'),
            'role_id' => 1,
            'active' => true,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        DB::table('users')->insert([
            'id' => 2,
            'name' => 'Project_Manager',
            'last_name' => 'Project_Manager',
            'maternal_surname' => 'Project_Manager',
            'email' => 'project_manager@gmail.com',
            'password' => bcrypt('123_Project_Manager'),
            'role_id' => 2,
            'active' => true,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        DB::table('users')->insert([
            'id' => 3,
            'name' => 'Developer',
            'last_name' => 'Developer',
            'maternal_surname' => 'Developer',
            'email' => 'developer@gmail.com',
            'password' => bcrypt('123_Developer'),
            'role_id' => 3,
            'active' => true,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }
}
