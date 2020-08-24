<?php

namespace Tests\Integration;

use App\Events\PurchaseCreated;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class UserPurchaseTest extends TestCase
{
    /** @test */
    public function user_can_purchase_a_key()
    {
        $key = factory(Key::class)->state('test')->create(['owner_id' => $this->seller]);

        $this->assertFalse(Purchase::where('key_id', $key->id)->exists());
        $this->buyer->purchase($key);
        $this->assertTrue(Purchase::where('key_id', $key->id)->exists());
    }

    /** @test */
    public function purchasing_a_key_fires_purchase_created_event()
    {
        $key = factory(Key::class)->state('test')->create();
        $buyer = factory(User::class)->state('buyer')->create();

        Event::fake();
        $buyer->purchase($key);
        Event::assertDispatched(PurchaseCreated::class);
    }

    /** @test */
    public function purchasing_a_key_increases_seller_balance()
    {
        $key = factory(Key::class)->state('test')->create();
        $buyer = factory(User::class)->state('buyer')->create();

        $this->assertEquals(0, $key->owner->balance);
        $buyer->purchase($key);
        $this->assertEquals($key->game->priceIncludingCommission(), $key->owner->balance);
    }
}
