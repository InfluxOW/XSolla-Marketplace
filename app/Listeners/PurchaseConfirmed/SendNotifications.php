<?php

namespace App\Listeners\PurchaseConfirmed;

use App\Events\PurchaseConfirmed;
use App\Jobs\NotifySellerAboutSoldKey;
use App\Jobs\SendMail;
use App\Mail\SendKeyToTheBuyer;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendNotifications
{
    public function handle(PurchaseConfirmed $event)
    {
        NotifySellerAboutSoldKey::dispatch($event->purchase)->onQueue('notifications');
        SendMail::dispatch($event->purchase->buyer, new SendKeyToTheBuyer($event->purchase))->onQueue('mails');
    }
}
