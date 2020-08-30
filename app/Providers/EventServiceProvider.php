<?php

namespace App\Providers;

use App\Events\PaymentConfirmed;
use App\Listeners\PurchaseConfirmed\IncreaseSellerBalance;
use App\Listeners\PurchaseConfirmed\SendNotifications;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        PaymentConfirmed::class => [
            IncreaseSellerBalance::class,
            SendNotifications::class
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
