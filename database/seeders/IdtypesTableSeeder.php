<?php

namespace Database\Seeders;

use App\Models\Idtype;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class IdtypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $db = Idtype::query();
        $db->truncate();

        $types = [
            ['name' =>  'ACT Driver License', 'slug' => 'act_driver_license', 'issuer'=> 'DHA', 'state'=> 'AUS', 'country_id'=>14],
            ['name' =>  'Australia Visa', 'slug' =>'nsw_driver_license', 'issuer' =>'LICENSING SERVICES WA', 'state' =>'WA', 'country_id' => 14],
            ['name' =>  'NSW Driver License', 'slug' =>'nsw_photo_id', 'issuer' =>'QLD DTMR', 'state' =>'QLD', 
            'country_id' => 14],
            ['name' =>  'NSW Photo ID', 'slug' =>'nt_driver_license', 'issuer' =>'RTA NSW', 'state' =>'NSW', 
            'country_id' => 14],
            ['name' =>  'NT Driver License', 'slug' =>'passport_australia', 'issuer' =>'SA DTEI', 'state' =>'SA', 
            'country_id' => 14],
            ['name' =>  'Passport Australia', 'slug' =>'photo_id_tasmania', 'issuer' =>'VICROADS', 'state' =>'VIC', 'country_id' => 14],
            ['name' =>  'Photo ID Tasmania', 'slug' =>'qld_driver_license', 'issuer' =>'ACT RTA', 'state' =>'ACT', 
            'country_id' => 14],
            ['name' =>  'QLD Driver License', 'slug' =>'qld_proof_of_age_card', 'issuer' =>'MVR NT', 'state' =>'NT', 'country_id' => 14],
            ['name' =>  'QLD proof of age card', 'slug' =>'sa_driver_license', 'issuer' =>'AUSTRALIA GOVERNMENT', 'state' =>'AUS', 'country_id' => 14],
            ['name' =>  'SA Driver License', 'slug' =>'sa_proof_age_card', 'issuer' =>'DPC TAS', 'state' =>'TAS', 
            'country_id' => 14],
            ['name' =>  'SA proof of age card', 'slug' =>'tansmania_driver_license', 'issuer' =>'RTA NSW', 'state' =>'NSW', 'country_id' => 14],
            ['name' =>  'Tasmania Driver License', 'slug' =>'victoria_driver_license', 'issuer' =>'DPC TAS', 'state' =>'TAS', 'country_id' => 14],
            ['name' =>  'Victoria Driver License', 'slug' =>'visa_australisa', 'issuer' =>'QLD DTMR', 'state' =>'QLD', 'country_id' => 14],
            ['name' =>  'WA Driver License', 'slug' =>'wa_driver_license', 'issuer' =>'SA DTEI', 'state' => 'SA', 'country_id'=>14],
        ];

        $db->insert($types);
    }
}
