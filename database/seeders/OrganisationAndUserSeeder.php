<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Organisation;
use Illuminate\Database\Seeder;

class OrganisationAndUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Generate 3 organisations
        $organisations = Organisation::factory(3)->create();

        foreach ($organisations as $org) {
            // Set current organisation context statically so that the boots/factories register properly
            \App\Traits\BelongsToOrganisation::setCurrentOrganisationId($org->id);

            // Create Org Admin
            User::factory()->create([
                'role' => 'org_admin',
                'organisation_id' => $org->id,
                'password' => bcrypt('password'),
            ]);

            // Create Manager
            User::factory()->create([
                'role' => 'manager',
                'organisation_id' => $org->id,
                'password' => bcrypt('password'),
            ]);

            // Create Staff
            User::factory(2)->create([
                'role' => 'staff',
                'organisation_id' => $org->id,
                'password' => bcrypt('password'),
            ]);
        }

        // Clear context
        \App\Traits\BelongsToOrganisation::setCurrentOrganisationId(null);
    }
}
