<?php

/** @noinspection StaticInvocationViaThisInspection */

use App\Purchase;
use Illuminate\Database\Seeder;

class PurchaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Purchase::class, 30)->create();
    }
}
