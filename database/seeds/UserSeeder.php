<?php

namespace Database\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->delete();
        
        DB::table('users')->insert(array (
            0 => 
            array (
                'user_type_id' => 1,
                'email'      => 'customer1@gmail.com',
                'fullname'   => 'Customer 1',
                'password'   => '$2y$12$7zw.h44/b1dE2b1pQbze/OAo.AUJaNuz9b7ENPcpnWmtifIL3rD3C',
                'created_at' => '2021-04-01 00:00:00',
                'updated_at' => NULL,
            ),
            1 => 
            array (
                'user_type_id' => 2,
                'email'      => 'staff1@gmail.com',
                'fullname'   => 'Staff 1',
                'password'   => '$2y$12$7zw.h44/b1dE2b1pQbze/OAo.AUJaNuz9b7ENPcpnWmtifIL3rD3C',
                'created_at' => '2021-04-01 00:00:00',
                'updated_at' => NULL,
            ),
            2 => 
            array (
                'user_type_id' => 2,
                'email'      => 'staff2@gmail.com',
                'fullname'   => 'Staff 2',
                'password'   => '$2y$12$7zw.h44/b1dE2b1pQbze/OAo.AUJaNuz9b7ENPcpnWmtifIL3rD3C',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            3 => 
            array (
                'user_type_id' => 1,
                'email'      => 'customer2@gmail.com',
                'fullname'   => 'Customer 2',
                'password'   => '$2y$12$7zw.h44/b1dE2b1pQbze/OAo.AUJaNuz9b7ENPcpnWmtifIL3rD3C',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            4 => 
            array (
                'user_type_id' => 1,
                'email'      => 'customer3@gmail.com',
                'fullname'   => 'Customer 3',
                'password'   => '$2y$12$7zw.h44/b1dE2b1pQbze/OAo.AUJaNuz9b7ENPcpnWmtifIL3rD3C',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => NULL,
            )
        ));
    }
}
