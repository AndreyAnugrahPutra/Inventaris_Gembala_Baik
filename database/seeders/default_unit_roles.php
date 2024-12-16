<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class default_unit_roles extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::table('role')->insert([
            'nama_role' => 'admin',
            'created_at' => Carbon::now('Asia/Jayapura'),
        ]);
        DB::table('role')->insert([
            'nama_role' => 'guru',
            'created_at' => Carbon::now('Asia/Jayapura'),
        ]);
        DB::table('role')->insert([
            'nama_role' => 'kepsek',
            'created_at' => Carbon::now('Asia/Jayapura'),
        ]);
        DB::table('role')->insert([
            'nama_role' => 'bendahara',
            'created_at' => Carbon::now('Asia/Jayapura'),
        ]);

        DB::table('unit')->insert([
            'id_unit' => 'unit-2024-001',
            'nama_unit' => 'tata usaha',
            'created_at' => Carbon::now('Asia/Jayapura'),
        ]);

        DB::table('unit')->insert([
            'id_unit' => 'unit-2024-002',
            'nama_unit' => 'keuangan',
            'created_at' => Carbon::now('Asia/Jayapura'),
        ]);

        DB::table('unit')->insert([
            'id_unit' => 'unit-2024-003',
            'nama_unit' => 'lab komputer',
            'created_at' => Carbon::now('Asia/Jayapura'),
        ]);
    }
}
