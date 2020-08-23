<?php

namespace App\Listeners;

use App\Events\PurchaseCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class IncreaseSellerBalance
{
    /**
     * Handle the event.
     *
     * @param  PurchaseCreated  $event
     * @return void
     */
    public function handle(PurchaseCreated $event)
    {
        $key = $event->purchase->key;

        $key->owner->increment('balance', $key->game->getPriceIncludingCommission());
    }
}
