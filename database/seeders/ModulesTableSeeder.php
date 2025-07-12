<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModulesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('modules')->insertOrIgnore([
            ['nom' => 'Mathématiques'],
            ['nom' => 'Physique'],
            ['nom' => 'Informatique'],
            ['nom' => 'Anglais'],
        ]);
    }
}
