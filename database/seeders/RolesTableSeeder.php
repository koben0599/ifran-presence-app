<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('roles')->insertOrIgnore([
            ['nom' => 'admin'],
            ['nom' => 'coordinateur'],
            ['nom' => 'enseignant'],
            ['nom' => 'etudiant'],
        ]);
    }
}
