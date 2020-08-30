<?php

/** @noinspection StaticInvocationViaThisInspection */

use App\Payment;
use Illuminate\Database\Seeder;

class PaymentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Payment::class, 30)->create();
    }
}
