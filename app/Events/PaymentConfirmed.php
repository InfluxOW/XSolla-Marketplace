<?php

namespace App\Events;

use App\Payment;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentConfirmed
{
    use Dispatchable;
    use SerializesModels;

    public Payment $purchase;

    public function __construct(Payment $purchase)
    {
        $this->purchase = $purchase;
    }
}
