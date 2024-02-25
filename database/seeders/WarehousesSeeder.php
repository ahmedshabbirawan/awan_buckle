<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class WarehousesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(){
        \DB::table('warehouses')->insert(array (
            array (
                'id' => 1,
                'name' => 'Warehouse 1',
                'code' => 'wh-01',
                'address' => 'Lahore, Punjab',
                'created_at' => '2022-01-01 00:00:01',
                'updated_at' => '2022-01-01 00:00:01',
            )
        ));
    }
}
