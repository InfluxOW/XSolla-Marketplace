<?php

namespace Tests\Integration;

use App\Events\PaymentConfirmed;
use App\Jobs\NotifySellerAboutSoldKey;
use App\Jobs\SendMail;
use App\Key;
use App\Listeners\PurchaseConfirmed\IncreaseSellerBalance;
use App\Listeners\PurchaseConfirmed\SendNotifications;
use App\Mail\SendKeyToThePayer;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class UserPurchaseTest extends TestCase
{
    protected $buyer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->buyer = factory(User::class)->state('buyer')->create();
        $this->key = factory(Key::class)->state('test')->create();
    }

    /** @test */
    public function reserved_key_becomes_unavailable_once_user_confirms_purchase()
    {
        $purchase = $this->buyer->reserve($this->key);
        Event::fake();

        $purchase->confirm();

        $this->assertFalse($this->key->isAvailable());
    }

    /** @test */
    public function purchase_confirmation_fires_purchase_confirmed_event()
    {
        $purchase = $this->buyer->reserve($this->key);
        Event::fake();

        $purchase->confirm();

        Event::assertDispatched(PaymentConfirmed::class);
    }

    /** @test */
    public function purchase_confirmation_triggers_increase_seller_balance_listener()
    {
        $purchase = $this->buyer->reserve($this->key);
        Event::fake(PaymentConfirmed::class);

        $purchase->confirm();

        $listener = \Mockery::mock(IncreaseSellerBalance::class);
        $listener->shouldReceive('handle');
    }

    /** @test */
    public function increase_seller_balance_listener_increases_seller_balance()
    {
        $event = $this->mockPurchaseConfirmedEvent();
        $key = $event->purchase->key;

        $this->assertEquals(0, $key->owner->balance);

        $listener = app()->make(IncreaseSellerBalance::class);
        $listener->handle($event);

        $this->assertEquals($key->game->getPriceIncludingCommission(), $key->fresh()->owner->balance);
    }

    /** @test */
    public function purchase_confirmation_triggers_send_notifications_listener()
    {
        $purchase = $this->buyer->reserve($this->key);
        Event::fake(PaymentConfirmed::class);

        $purchase->confirm();

        $listener = \Mockery::mock(SendNotifications::class);
        $listener->shouldReceive('handle');
    }

    /** @test */
    public function send_notifications_listener_dispatches_notify_seller_about_sold_key_and_send_mail_jobs()
    {
        $event = $this->mockPurchaseConfirmedEvent();

        Queue::fake();

        $listener = app()->make(SendNotifications::class);
        $listener->handle($event);

        Queue::assertPushed(NotifySellerAboutSoldKey::class);
        Queue::assertPushed(SendMail::class);
    }

    /** @test */
    public function notify_seller_about_sold_key_job_sends_http_request_to_the_seller_server()
    {
        $purchase = $this->buyer->reserve($this->key);

        Http::fake([
            '*' => Http::response([], 201),
        ]);

        NotifySellerAboutSoldKey::dispatch($purchase);

        Http::assertSentCount(1);
    }

    /** @test */
    public function send_notifications_listener_dispatches_send_mail_job_with_purchase_buyer_and_send_key_to_the_payer_mailable()
    {
        $event = $this->mockPurchaseConfirmedEvent();
        $listener = app()->make(SendNotifications::class);

        Bus::fake(NotifySellerAboutSoldKey::class);
        Mail::fake();

        $listener->handle($event);

        Mail::assertSent(SendKeyToThePayer::class);
    }

    protected function mockPurchaseConfirmedEvent()
    {
        $purchase = $this->buyer->reserve($this->key);

        $event = \Mockery::mock(PaymentConfirmed::class);
        $event->purchase = $purchase;

        return $event;
    }
}
