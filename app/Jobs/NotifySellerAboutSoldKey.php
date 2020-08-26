<?php

namespace App\Jobs;

use App\Events\PurchaseConfirmed;
use App\Purchase;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NotifySellerAboutSoldKey implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 60;
    public Purchase $purchase;

    public function __construct(Purchase $purchase)
    {
        $this->purchase = $purchase;
    }

    public function handle()
    {
        $message = [
            'message' => 'Your key has been bought!',
            'key' => $this->purchase->key->serial_number,
            'buyer' => [
                'name' => $this->purchase->buyer->name,
                'email' => $this->purchase->buyer->email
            ],
            'commission' => config('app.marketplace.commission') * $this->purchase->key->game->price,
            'verifier' => bcrypt("{$this->purchase->key->serial_number}/{$this->purchase->confirmed_at}"),
        ];

        $response = Http::post(
            route('users.sales.store', ['user' => $this->purchase->key->owner]),
            $message
        );

        if ($response->failed()) {
            $this->fail();
            return;
        }

        Log::info("Purchase {$this->purchase->id} has been proceeded.");
    }
}
