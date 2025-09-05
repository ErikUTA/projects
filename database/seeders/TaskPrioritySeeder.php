<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TaskPrioritySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('task_priorities')->insert([
            'id' => 1,
            'name' => 'Low',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        DB::table('task_priorities')->insert([
            'id' => 2,
            'name' => 'Medium',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        DB::table('task_priorities')->insert([
            'id' => 3,
            'name' => 'High',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }
}
