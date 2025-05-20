<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sql = storage_path('sql_dumps/countries.sql');

        // Check if the file exists
        if (file_exists($sql)) {
            DB::unprepared(file_get_contents($sql));
        } else {
            $this->command->error("This file does not exist: {$sql}");
        }
    }
}
