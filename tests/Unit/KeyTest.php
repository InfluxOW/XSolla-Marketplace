<?php

namespace Tests\Unit;

use App\Distributor;
use App\Game;
use App\Key;
use App\Purchase;
use App\User;
use Tests\TestCase;

class KeyTest extends TestCase
{
    protected $key;

    protected function setUp():void
    {
        parent::setUp();

        $this->key = factory(Key::class)->state('test')->create();
    }

    /** @test */
    public function it_belongs_to_a_game()
    {
        $this->assertInstanceOf(Game::class, $this->key->game);
    }

    /** @test */
    public function it_belongs_to_a_distributor()
    {
        $this->assertInstanceOf(Distributor::class, $this->key->distributor);
    }

    /** @test */
    public function it_belongs_to_an_owner()
    {
        $this->assertInstanceOf(User::class, $this->key->owner);
    }

    /** @test */
    public function it_may_have_a_purchase()
    {
        $purchase = factory(Purchase::class)->state('test')->create(['key_id' => $this->key]);

        $this->assertTrue($this->key->purchase->is($purchase));
        $this->assertInstanceOf(Purchase::class, $this->key->purchase);
    }

    /** @test */
    public function it_knows_if_it_is_available()
    {
        $this->assertTrue($this->key->isAvailable());

        $buyer = factory(User::class)->state('buyer')->create();
        $buyer->purchase($this->key);

        $this->assertFalse($this->key->isAvailable());
    }

    /** @test */
    public function it_can_be_scoped_to_only_available_keys()
    {
        $available = $this->key;
        $unavailable = factory(Key::class)->state('test')->create();
        $buyer = factory(User::class)->state('buyer')->create();
        $buyer->purchase($unavailable);

        $this->assertTrue(Key::available()->get()->contains($available));
        $this->assertFalse(Key::available()->get()->contains($unavailable));
    }
}
