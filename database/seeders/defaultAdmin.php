<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class defaultAdmin extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::table('users')->insert([
            'id_user' => 'id_001',
            'username' => 'glory',
            'email' => 'glory@gmail.com',
            'password' => 'admin',
            'id_role' => 1,
            'id_unit' => 'unit-2024-001',
            'created_at' => Carbon::now('Asia/Jayapura'),
        ]);
    }
}
