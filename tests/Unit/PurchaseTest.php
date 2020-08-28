<?php

namespace Tests\Unit;

use App\Key;
use App\Purchase;
use App\User;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class PurchaseTest extends TestCase
{
    protected $purchase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->purchase = factory(Purchase::class)->state('test')->create();
    }

    /** @test */
    public function it_belongs_to_a_key()
    {
        $this->assertInstanceOf(Key::class, $this->purchase->key);
    }

    /** @test */
    public function it_belongs_to_a_buyer()
    {
        $this->assertInstanceOf(User::class, $this->purchase->buyer);
    }

    /** @test */
    public function it_has_a_seller()
    {
        $this->assertInstanceOf(User::class, $this->purchase->seller);
    }

    /** @test */
    public function it_knows_if_it_is_confirmed_or_unconfirmed()
    {
        $this->purchase->update(['confirmed_at' => null]);

        $this->assertFalse($this->purchase->isConfirmed());
        $this->assertTrue($this->purchase->isUnconfirmed());

        Event::fake();
        Bus::fake();
        $this->purchase->confirm();

        $this->assertTrue($this->purchase->isConfirmed());
        $this->assertFalse($this->purchase->isUnconfirmed());
    }

    /** @test */
    public function it_can_be_scoped_to_only_confirmed_purchases()
    {
        $confirmed = $this->purchase;
        $unconfirmed = factory(Purchase::class)->state('test')->create(['confirmed_at' => null]);

        $this->assertTrue(Purchase::confirmed()->get()->contains($confirmed));
        $this->assertFalse(Purchase::confirmed()->get()->contains($unconfirmed));
        $this->assertTrue(Purchase::unconfirmed()->get()->contains($unconfirmed));
        $this->assertFalse(Purchase::unconfirmed()->get()->contains($confirmed));
    }
}
