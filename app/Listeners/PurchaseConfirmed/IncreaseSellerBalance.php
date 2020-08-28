<?php

namespace App\Listeners\PurchaseConfirmed;

use App\Events\PurchaseConfirmed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class IncreaseSellerBalance
{
    public function handle(PurchaseConfirmed $event)
    {
        $key = $event->purchase->key;

        $key->owner->increment('balance', $key->game->getPriceIncludingCommission());
    }
}
