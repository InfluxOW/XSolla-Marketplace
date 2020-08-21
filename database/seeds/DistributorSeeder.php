<?php

use App\Distributor;
use Illuminate\Database\Seeder;

class DistributorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Distributor::class)->state('steam')->create();
        factory(Distributor::class)->state('egs')->create();
        factory(Distributor::class)->state('ps store')->create();
        factory(Distributor::class)->state('gog')->create();
    }
}
