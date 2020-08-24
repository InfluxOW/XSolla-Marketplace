<?php

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
         switch (app('env')) {
             case 'production':
                 $this->call([
                     DistributorSeeder::class,
                 ]);
                 break;
             default:
                 $this->call([
                     UserSeeder::class,
                     DistributorSeeder::class,
                     GameSeeder::class,
                     KeySeeder::class,
                     PurchaseSeeder::class,
                 ]);
                 break;
         }
    }
}
