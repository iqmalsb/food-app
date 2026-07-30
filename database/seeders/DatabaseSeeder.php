<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        $org = \App\Models\Organisation::firstOrCreate(
            ['slug' => 'savor-co'],
            [
                'name' => 'Savor & Co.',
                'seats_limit' => 5,
            ]
        );

        // Set current organization context so that category, food, and table seeds get associated with this org
        \App\Traits\BelongsToOrganisation::setCurrentOrganisationId($org->id);

        $this->call(CategorySeeder::class);
        $this->call(FoodSeeder::class);
        $this->call(TableSeeder::class);

        // Create Superadmin User (Global Owner)
        \App\Models\User::firstOrCreate(
            ['email' => 'superadmin@foodapp.com'],
            [
                'name' => 'Superadmin User',
                'password' => bcrypt('password'),
                'role' => 'superadmin',
                'organisation_id' => null,
                'email_verified_at' => now(),
            ]
        );

                // Create Org Admin User (Merchant Root User)
        \App\Models\User::firstOrCreate(
            ['email' => 'admin@foodapp.com'],
            [
                'name' => 'Org Admin Savor',
                'password' => bcrypt('password'),
                'role' => 'org_admin',
                'organisation_id' => $org->id,
                'email_verified_at' => now(),
            ]
        );

        $this->call(OrganisationAndUserSeeder::class);
    }
}
