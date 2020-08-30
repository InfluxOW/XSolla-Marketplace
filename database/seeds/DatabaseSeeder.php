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
                    DistributorsSeeder::class,
                ]);
                break;
            default:
                $this->call([
                    UsersSeeder::class,
                    DistributorsSeeder::class,
                    GamesSeeder::class,
                    KeysSeeder::class,
                    PaymentsSeeder::class,
                ]);
                break;
        }
    }
}
