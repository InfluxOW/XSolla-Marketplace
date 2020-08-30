<?php

namespace App\Listeners\PurchaseConfirmed;

use App\Events\PaymentConfirmed;
use App\Jobs\NotifySellerAboutSoldKey;
use App\Jobs\SendMail;
use App\Mail\SendKeyToThePayer;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendNotifications
{
    public function handle(PaymentConfirmed $event)
    {
        NotifySellerAboutSoldKey::dispatch($event->purchase)->onQueue('notifications');
        SendMail::dispatch($event->purchase->payer, new SendKeyToThePayer($event->purchase))->onQueue('mails');
    }
}
