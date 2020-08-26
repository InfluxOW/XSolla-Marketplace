<?php

namespace Tests\Unit;

use App\Distributor;
use App\Game;
use App\Key;
use App\Platform;
use App\Purchase;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
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
    public function it_may_have_purchases()
    {
        $purchases = factory(Purchase::class, 2)->state('test')->create(['key_id' => $this->key, 'confirmed_at' => null]);

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
    public function once_a_key_has_confirmed_purchase_it_becomes_unavailable()
    {
        $this->assertTrue($this->key->isAvailable());
        $unconfirmedPurchase = factory(Purchase::class)->state('test')->create(['key_id' => $this->key, 'confirmed_at' => null]);
        $this->assertTrue($this->key->isAvailable());
        $confirmedPurchase = factory(Purchase::class)->state('test')->create(['key_id' => $this->key, 'confirmed_at' => now()]);
        $this->assertFalse($this->key->fresh()->isAvailable());
    }

    /** @test */
    public function it_can_be_scoped_to_only_available_keys()
    {
        $available = $this->key;
        $unconfirmedPurchase = factory(Purchase::class)->state('test')->create(['key_id' => $available, 'confirmed_at' => null]);

        $unavailable = factory(Key::class)->state('test')->create();
        $confirmedPurchase = factory(Purchase::class)->state('test')->create(['key_id' => $unavailable, 'confirmed_at' => now()]);

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

    /** @test */
    public function many_keys_can_be_created_by_request()
    {
        $platform = factory(Platform::class)->create();
        $game = factory(Game::class)->state('test')->create(['platform_id' => $platform]);
        $distributor = factory(Distributor::class)->state('test')->create(['platform_id' => $platform]);
        $keys = ['HGSD-235A-HSDH-HKS9', 'HGSD-235A-HSDH-HKS8'];

        $request = new Request();
        $request->replace(compact('game', 'distributor', 'keys'));
        $request->setUserResolver(function (){
            return factory(User::class)->state('seller')->create();
        });
        $keys = Key::createManyByRequest($request);

        $this->assertDatabaseHas('keys', ['serial_number' => $keys->first()->serial_number]);
        $this->assertDatabaseHas('keys', ['serial_number' => $keys->second()->serial_number]);
    }
}
