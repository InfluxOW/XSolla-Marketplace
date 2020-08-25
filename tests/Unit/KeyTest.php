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
    public function it_has_purchases()
    {
        $purchases = factory(Purchase::class, 2)->state('test')->create(['key_id' => $this->key, 'made_at' => null]);

        $this->assertTrue(
            $this->key->purchases->contains($purchases->first() ||
            $this->key->purchases->contains($purchases->second())
        ));
        $this->assertInstanceOf(Purchase::class, $this->key->purchases->first());
    }

    /** @test */
    public function once_a_key_has_completed_purchase_it_becomes_unavailable()
    {
        $this->assertTrue($this->key->isAvailable());
        $incompletedPurchase = factory(Purchase::class)->state('test')->create(['key_id' => $this->key, 'made_at' => null]);
        $this->assertTrue($this->key->isAvailable());
        $completedPurchase = factory(Purchase::class)->state('test')->create(['key_id' => $this->key]);
        $this->assertFalse($this->key->fresh()->isAvailable());
    }

    /** @test */
    public function it_can_be_scoped_to_only_available_keys()
    {
        $available = $this->key;
        $incompletedPurchase = factory(Purchase::class)->state('test')->create(['key_id' => $available, 'made_at' => null]);

        $unavailable = factory(Key::class)->state('test')->create();
        $completedPurchase = factory(Purchase::class)->state('test')->create(['key_id' => $unavailable]);

        $this->assertTrue(Key::available()->get()->contains($available->fresh()));
        $this->assertFalse(Key::available()->get()->contains($unavailable->fresh()));
    }
}
