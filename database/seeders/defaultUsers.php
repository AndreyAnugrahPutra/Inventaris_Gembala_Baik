<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class defaultUsers extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //buat users default
        DB::table('users')->insert([
            'username' => 'admin01',
            'nama' => fake()->name(),
            'jkel' => 'Laki-laki',
            'email' => fake()->unique()->safeEmail(),
            'password' => Hash::make('admin01'),
            'role' => 'admin',
            'foto_profil' => null,
            'remember_token' => null,
            'created_at' => Carbon::now('Asia/Jayapura'),
        ]);
        DB::table('users')->insert([
            'username' => 'admin02',
            'nama' => fake()->name(),
            'jkel' => 'Laki-laki',
            'email' => fake()->unique()->safeEmail(),
            'password' => Hash::make('admin02'),
            'role' => 'admin',
            'foto_profil' => null,
            'is_login' => 0,
            'remember_token' => null,
            'created_at' => Carbon::now('Asia/Jayapura'),
        ]);
        DB::table('users')->insert([
            'username' => 'officer01',
            'nama' => fake()->name(),
            'jkel' => 'Laki-laki',
            'email' => fake()->unique()->safeEmail(),
            'password' => Hash::make('officer01'),
            'role' => 'officer',
            'foto_profil' => null,
            'is_login' => 0,
            'remember_token' => null,
            'created_at' => Carbon::now('Asia/Jayapura'),
        ]);
        DB::table('users')->insert([
            'username' => 'officer02',
            'nama' => fake()->name(),
            'jkel' => 'Laki-laki',
            'email' => fake()->unique()->safeEmail(),
            'password' => Hash::make('officer02'),
            'role' => 'officer',
            'foto_profil' => null,
            'is_login' => 0,
            'remember_token' => null,
            'created_at' => Carbon::now('Asia/Jayapura'),
        ]);
        DB::table('users')->insert([
            'username' => 'officer03',
            'nama' => fake()->name(),
            'jkel' => 'Laki-laki',
            'email' => fake()->unique()->safeEmail(),
            'password' => Hash::make('officer03'),
            'role' => 'officer',
            'foto_profil' => null,
            'is_login' => 0,
            'remember_token' => null,
            'created_at' => Carbon::now('Asia/Jayapura'),
        ]);
        DB::table('users')->insert([
            'username' => 'manager01',
            'nama' => fake()->name(),
            'jkel' => 'Laki-laki',
            'email' => fake()->unique()->safeEmail(),
            'password' => Hash::make('manager01'),
            'role' => 'manager',
            'foto_profil' => null,
            'is_login' => 0,
            'remember_token' => null,
            'created_at' => Carbon::now('Asia/Jayapura'),
        ]);
    }
}
