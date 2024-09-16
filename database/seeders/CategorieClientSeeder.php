<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorieClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('categories_clients')->insert([
            ['libelle' => 'Gold'],
            ['libelle' => 'Silver'],
            ['libelle' => 'Bronze'],
        ]);
    }
}
