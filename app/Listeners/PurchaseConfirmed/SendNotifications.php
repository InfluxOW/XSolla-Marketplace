<?php

namespace App\Listeners\PurchaseConfirmed;

use App\Events\PurchaseConfirmed;
use App\Jobs\NotifySellerAboutSoldKey;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendNotifications
{
    public function handle(PurchaseConfirmed $event)
    {
        NotifySellerAboutSoldKey::dispatch($event->purchase)->onQueue('notifiers');
    }
}
