<?php

namespace Database\Seeders;

use App\Models\DeactivateReason;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DeactivateReasonsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $deactivation_reason = DeactivateReason::query();
        $deactivation_reason->truncate();

        $reasons = [
            "This is temporary, I'll be back",
            "I don't understand how to use ".env('APP_NAME'),
            "I get too many emails, invitations and requests from ".env('APP_NAME'),
            "My Account was hacked",
            "I don't find ".env('APP_NAME')." useful",
            "I have a privacy concern",
            "I have another ".env('APP_NAME')." account",
            "Others, please explain further"
        ];

        foreach ($reasons as $key => $reason) {
            $deactivation_reason->create([
                'reason' => $reason
            ]);
        }
    }
}
