<?php

namespace App\Listeners;

use App\Events\PurchaseConfirmed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class IncreaseSellerBalance
{
    /**
     * Handle the event.
     *
     * @param  PurchaseConfirmed  $event
     * @return void
     */
    public function handle(PurchaseConfirmed $event)
    {
        $key = $event->purchase->key;

        $key->owner->increment('balance', $key->game->getPriceIncludingCommission());
    }
}
