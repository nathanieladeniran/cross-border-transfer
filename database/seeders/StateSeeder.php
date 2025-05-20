<?php

namespace Database\Seeders;

use App\Models\State;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        State::query()->truncate();
        DB::table('states')->insert([
            [
                'country_id' => 14,
                'name' => 'New South Wales',
                'abbr' => 'NSW',
            ],
            [
                'country_id' => 14,
                'name' => 'Victoria',
                'abbr' => 'VIC',
            ],
            [
                'country_id' => 14,
                'name' => 'Queensland',
                'abbr' => 'QLD',
            ],
            [
                'country_id' => 14,
                'name' => 'Tasmania',
                'abbr' => 'TAS',
            ],
            [
                'country_id' => 14,
                'name' => 'South Australia',
                'abbr' => 'SA',
            ],
            [
                'country_id' => 14,
                'name' => 'Western Australia',
                'abbr' => 'WA',
            ],
            [
                'country_id' => 14,
                'name' => 'Northern Territory',
                'abbr' => 'NT',
            ],
            [
                'country_id' => 14,
                'name' => 'Australian Capital Territory',
                'abbr' => 'ACT',
            ],
        ]);
    }
}
