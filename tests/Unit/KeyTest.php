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
    public function it_knows_if_it_has_been_reserved_by_the_specified_user()
    {
        $buyer = factory(User::class)->state('buyer')->create();

        $this->assertFalse($this->key->isReservedBy($buyer));
        $buyer->reserve($this->key);
        $this->assertTrue($this->key->fresh()->isReservedBy($buyer));
    }

    /** @test */
    public function once_a_key_has_completed_purchase_it_becomes_unavailable()
    {
        $this->assertTrue($this->key->isAvailable());
        $incompletedPurchase = factory(Purchase::class)->state('test')->create(['key_id' => $this->key, 'made_at' => null]);
        $this->assertTrue($this->key->isAvailable());
        $completedPurchase = factory(Purchase::class)->state('test')->create(['key_id' => $this->key, 'made_at' => now()]);
        $this->assertFalse($this->key->fresh()->isAvailable());
    }

    /** @test */
    public function it_can_be_scoped_to_only_available_keys()
    {
        $available = $this->key;
        $incompletedPurchase = factory(Purchase::class)->state('test')->create(['key_id' => $available, 'made_at' => null]);

        $unavailable = factory(Key::class)->state('test')->create();
        $completedPurchase = factory(Purchase::class)->state('test')->create(['key_id' => $unavailable, 'made_at' => now()]);

        $this->assertTrue(Key::available()->get()->contains($available->fresh()));
        $this->assertFalse(Key::available()->get()->contains($unavailable->fresh()));
    }

    /** @test */
    public function it_can_be_scoped_to_only_available_at_the_specified_distributor_keys()
    {
        $steam = factory(Distributor::class)->state('test')->create(['name' => 'Steam']);
        $availableAtSteamKey = factory(Key::class)->state('test')->create(['distributor_id' => $steam]);

        $psStore = factory(Distributor::class)->state('test')->create(['name' => 'PS Store']);
        $availableAtPsStoreKey = factory(Key::class)->state('test')->create(['distributor_id' => $psStore]);

        $this->assertTrue(Key::availableAtDistributor($steam->slug)->get()->contains($availableAtSteamKey));
        $this->assertFalse(Key::availableAtDistributor($steam->slug)->get()->contains($availableAtPsStoreKey));
        $this->assertTrue(Key::availableAtDistributor($psStore->slug)->get()->contains($availableAtPsStoreKey));
        $this->assertFalse(Key::availableAtDistributor($psStore->slug)->get()->contains($availableAtSteamKey));
    }
}
