<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        $this->call(StateSeeder::class);
        $this->call(CountrySeeder::class);
        $this->call(IdtypesTableSeeder::class);
        $this->call(BusinessCategorySeeder::class);
        $this->call(ShuftiProJusrisdictionSeeder::class);
        $this->call(DeactivateReasonsSeeder::class);

        Schema::enableForeignKeyConstraints();
    }
}
