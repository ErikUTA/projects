<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TaskStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('task_statuses')->insert([
            'id' => 1,
            'name' => 'Pending',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        DB::table('task_statuses')->insert([
            'id' => 2,
            'name' => 'In progress',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        DB::table('task_statuses')->insert([
            'id' => 3,
            'name' => 'Completed',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }
}
