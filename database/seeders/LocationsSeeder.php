<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class LocationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(){
        \DB::table('locations')->insert(array (
            array (
                'id' => 1,
                'name' => 'Pakistan',
                'loc_id' => '0',
                'type' => 'CO',
                'status' => '1',
                'created_at' => '2022-01-01 00:00:01',
                'updated_at' => '2022-01-01 00:00:01',
            ),
            array (
                'id' => 2,
                'name' => 'Punjab',
                'loc_id' => '1',
                'type' => 'PR',
                'status' => '1',
                'created_at' => '2022-01-01 00:00:01',
                'updated_at' => '2022-01-01 00:00:01',
            ),
            array (
                'id' => 3,
                'name' => 'Lahore',
                'loc_id' => '2',
                'type' => 'CI',
                'status' => '1',
                'created_at' => '2022-01-01 00:00:01',
                'updated_at' => '2022-01-01 00:00:01',
            )
        ));
    }
}
