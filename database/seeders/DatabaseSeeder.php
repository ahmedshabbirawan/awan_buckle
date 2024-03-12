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
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        $this->call([
            UserSeeder::class,
            // AttributeTypeSeeder::class,
            // MeasureUnitsSeeder::class,
            // LocationsSeeder::class,
            // OrganizationsSeeder::class,
            // WarehousesSeeder::class,
            // AttributeValueSeeder::class,
            // CategorySeeder::class
        ]);
    }
}
