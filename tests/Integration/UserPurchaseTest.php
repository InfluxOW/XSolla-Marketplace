<?php

namespace Tests\Integration;

use App\Events\PurchaseConfirmed;
use App\Jobs\NotifySellerAboutSoldKey;
use App\Key;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class UserPurchaseTest extends TestCase
{
    protected $buyer;

    protected function setUp():void
    {
        parent::setUp();

        $this->buyer = factory(User::class)->state('buyer')->create();
    }

    /** @test */
    public function user_can_reserve_a_key()
    {
        $key = factory(Key::class)->state('test')->create();

        $this->assertFalse($key->isReservedBy($this->buyer));
        $this->buyer->reserve($key);
        $this->assertTrue($key->fresh()->isReservedBy($this->buyer));
    }

    /** @test */
    public function key_keeps_being_available_when_user_reserves_it()
    {
        $key = factory(Key::class)->state('test')->create();

        $this->assertTrue($key->isAvailable());
        $this->buyer->reserve($key);
        $this->assertTrue($key->isAvailable());
    }

    /** @test */
    public function reserved_key_becomes_unavailable_once_user_confirms_purchase()
    {
        $key = factory(Key::class)->state('test')->create();
        $purchase = $this->buyer->reserve($key);

        Event::fake();
        Bus::fake();
        $purchase->confirm();

        $this->assertFalse($key->isAvailable());
    }

    /** @test */
    public function purchase_confirmation_fires_key_purchased_event()
    {
        $key = factory(Key::class)->state('test')->create();
        $purchase = $this->buyer->reserve($key);

        Bus::fake();
        Event::fake();
        $purchase->confirm();
        Event::assertDispatched(PurchaseConfirmed::class);
    }

    /** @test */
    public function seller_balance_is_increased_after_purchase_confirmation()
    {
        $key = factory(Key::class)->state('test')->create();
        $purchase = $this->buyer->reserve($key);

        Bus::fake();
        $this->assertEquals(0, $key->owner->balance);
        $purchase->confirm();
        $this->assertEquals($key->game->getPriceIncludingCommission(), $key->fresh()->owner->balance);
    }

    /** @test */
    public function purchase_confirmation_dispatches_notify_seller_about_sold_key_job()
    {
        Queue::fake();

        $key = factory(Key::class)->state('test')->create();
        $purchase = $this->buyer->reserve($key);
        $purchase->confirm();

        Queue::assertPushed(NotifySellerAboutSoldKey::class);
    }

    /** @test */
    public function seller_receives_message_to_his_server_after_purchase_confirmation()
    {
        $key = factory(Key::class)->state('test')->create();
        $purchase = $this->buyer->reserve($key);

        Http::fake([
            '*' => Http::response([], 201),
        ]);

        Log::shouldReceive('info')
            ->once();

        $purchase->confirm();
    }
}
