<?php

namespace Tests\Integration;

use App\Events\KeyPurchased;
use App\Key;
use App\Purchase;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
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

        $this->assertFalse(Purchase::where('key_id', $key->id)->exists());
        $this->buyer->reserve($key);
        $this->assertTrue(Purchase::where('key_id', $key->id)->exists());
    }

    /** @test */
    public function key_keeps_being_available_when_user_reserves_it()
    {
        $key = factory(Key::class)->state('test')->create();
        $this->buyer->reserve($key);

        $this->assertTrue($key->isAvailable());
    }

    /** @test */
    public function key_becomes_unavailable_once_user_reserves_it_and_pays_for()
    {
        $key = factory(Key::class)->state('test')->create();
        $token = $this->buyer->reserve($key)['payment_session_token'];
        $this->buyer->completePurchase($token);
        $this->assertFalse($key->isAvailable());
    }

    /** @test */
    public function completing_a_purchase_fires_key_purchased_event()
    {
        $key = factory(Key::class)->state('test')->create();
        $token = $this->buyer->reserve($key)['payment_session_token'];

        Event::fake();
        $this->buyer->completePurchase($token);
        Event::assertDispatched(KeyPurchased::class);
    }

    /** @test */
    public function completing_a_purchase_increases_seller_balance()
    {
        $key = factory(Key::class)->state('test')->create();
        $token = $this->buyer->reserve($key)['payment_session_token'];

        $this->assertEquals(0, $key->owner->balance);
        $this->buyer->completePurchase($token);
        $this->assertEquals($key->game->getPriceIncludingCommission(), $key->fresh()->owner->balance);
    }
}
