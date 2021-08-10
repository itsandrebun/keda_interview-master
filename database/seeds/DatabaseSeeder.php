<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeds\UserTypeSeeder;
use Database\Seeds\UserSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call([UserTypeSeeder::class]);
        $this->call([UserSeeder::class]);
    }
}
