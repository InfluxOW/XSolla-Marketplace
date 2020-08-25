<?php

namespace App\Listeners;

use App\Events\KeyPurchased;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class IncreaseSellerBalance
{
    /**
     * Handle the event.
     *
     * @param  KeyPurchased  $event
     * @return void
     */
    public function handle(KeyPurchased $event)
    {
        $key = $event->purchase->key;

        $key->owner->increment('balance', $key->game->getPriceIncludingCommission());
    }
}
