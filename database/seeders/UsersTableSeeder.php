<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {



        \DB::table('users')->delete();

        \DB::table('users')->insert(array (

            0 =>
            array (
                'id' => 1,
                'name' => 'Super Admin',
                'email' => 'superadmin@pitb',
                'email_verified_at' => NULL,
                'password' => '$2y$10$OURI7FLfOFOON3OFudqHOOencdd2eJ8qgnhYpFMC8mU1F8mYAwqq2',
                'remember_token' => NULL,
                'created_at' => '2022-03-02 07:42:09',
                'updated_at' => '2022-03-02 07:42:09',
            )
        ));


       

        


    }
}
