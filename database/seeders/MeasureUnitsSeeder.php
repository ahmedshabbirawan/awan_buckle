<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class MeasureUnitsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(){
        \DB::table('measure_units')->insert(array (
            /*
            array (
                'id' => 1,
                'name' => 'Packet',
                'code' => 'Pkt.',
                'status' => '1',
                'created_at' => '2022-03-02 07:42:09',
                'updated_at' => '2022-03-02 07:42:09',
            ),
            array (
                'id' => 2,
                'name' => 'Dozen',
                'code' => 'Doz.',
                'status' => '1',
                'created_at' => '2022-03-02 07:42:09',
                'updated_at' => '2022-03-02 07:42:09',
            ),
            array (
                'id' => 3,
                'name' => 'Pieces',
                'code' => 'Pcs.',
                'status' => '1',
                'created_at' => '2022-03-02 07:42:09',
                'updated_at' => '2022-03-02 07:42:09',
            ),
            array (
                'id' => 4,
                'name' => 'Number',
                'code' => 'No.',
                'status' => '1',
                'created_at' => '2022-03-02 07:42:09',
                'updated_at' => '2022-03-02 07:42:09',
            ),
            array (
                'id' => 5,
                'name' => 'Quantity',
                'code' => 'Qty.',
                'status' => '1',
                'created_at' => '2022-03-02 07:42:09',
                'updated_at' => '2022-03-02 07:42:09',
            ),
            array (
                'id' => 6,
                'name' => 'Box',
                'code' => 'Box.',
                'status' => '1',
                'created_at' => '2022-03-02 07:42:09',
                'updated_at' => '2022-03-02 07:42:09',
            ),
            array (
                'id' => 7,
                'name' => 'Kilogram',
                'code' => 'Kg',
                'status' => '1',
                'created_at' => '2022-03-02 07:42:09',
                'updated_at' => '2022-03-02 07:42:09',
            ),
            array (
                'id' => 8,
                'name' => 'Jar',
                'code' => 'jar',
                'status' => '1',
                'created_at' => '2022-03-02 07:42:09',
                'updated_at' => '2022-03-02 07:42:09',
            ),
            array (
                'id' => 9,
                'name' => 'Feet',
                'code' => 'Ft',
                'status' => '1',
                'created_at' => '2022-03-02 07:42:09',
                'updated_at' => '2022-03-02 07:42:09',
            ),
            array (
                'id' => 10,
                'name' => 'Square Feet',
                'code' => 'SqFt',
                'status' => '1',
                'created_at' => '2022-03-02 07:42:09',
                'updated_at' => '2022-03-02 07:42:09',
            ),
            array (
                'id' => 11,
                'name' => 'Cubic Feet',
                'code' => 'CuFt',
                'status' => '1',
                'created_at' => '2022-03-02 07:42:09',
                'updated_at' => '2022-03-02 07:42:09',
            ),
            */
            array (
                'id' => 1,
                'name' => 'Pair',
                'code' => 'pair',
                'status' => '1',
                'created_at' => '2022-03-02 07:42:09',
                'updated_at' => '2022-03-02 07:42:09',
            ),
        ));
    }
}
