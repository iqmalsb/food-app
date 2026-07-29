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
        $this->call(FoodSeeder::class);
        $this->call(CategorySeeder::class);
        $this->call(TableSeeder::class);

        \App\Models\User::firstOrCreate(
            ['email' => 'admin@foodapp.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );
    }
}
