<?php

/** @noinspection StaticInvocationViaThisInspection */

namespace Tests\Unit;

use App\Distributor;
use App\Game;
use App\Key;
use App\Platform;
use App\Payment;
use App\User;
use Tests\TestCase;

class GameTest extends TestCase
{
    protected $game;

    protected function setUp(): void
    {
        parent::setUp();

        $this->game = factory(Game::class)->state('test')->create();
    }

    /** @test */
    public function it_belongs_to_a_platform()
    {
        $this->assertInstanceOf(Platform::class, $this->game->platform);
    }

    /** @test */
    public function it_has_keys()
    {
        $key = factory(Key::class)->state('test')->create(['game_id' => $this->game]);

        $this->assertInstanceOf(Key::class, $this->game->keys->first());
        $this->assertTrue($this->game->keys->contains($key));
    }

    /** @test */
    public function it_has_distributors_through_its_keys()
    {
        $distributor = factory(Distributor::class)->state('test')->create();
        $key = factory(Key::class)->state('test')->create(['game_id' => $this->game, 'distributor_id' => $distributor]);

        $this->assertInstanceOf(Distributor::class, $this->game->distributors->first());
        $this->assertTrue($this->game->distributors->contains($distributor));
    }

    /** @test */
    public function it_has_sales_through_its_keys()
    {
        $key = factory(Key::class)->state('test')->create(['game_id' => $this->game]);
        $purchase = factory(Payment::class)->state('test')->create(['key_id' => $key]);

        $this->assertInstanceOf(Payment::class, $this->game->sales->first());
        $this->assertTrue($this->game->sales->contains($purchase));
    }

    /** @test */
    public function it_can_be_scoped_to_games_that_are_available_for_purchase()
    {
        $availableGame = $this->game;
        $availableKey = factory(Key::class)->state('test')->create(['game_id' => $availableGame]);

        $unavailableGame = factory(Game::class)->state('test')->create();
        $unavailableKey = factory(Key::class)->state('test')->create(['game_id' => $unavailableGame]);
        $purchase = factory(Payment::class)->state('test')->create(['key_id' => $unavailableKey]);

        $this->assertTrue(Game::available()->get()->contains($availableGame));
        $this->assertFalse(Game::available()->get()->contains($unavailableGame));
    }

    /** @test */
    public function it_can_be_scoped_to_games_that_are_available_for_purchase_at_the_specific_distributor()
    {
        $availableGame = $this->game;
        $availableDistributor = factory(Distributor::class)->state('test')->create();
        $availableKey = factory(Key::class)->state('test')->create(['game_id' => $availableGame, 'distributor_id' => $availableDistributor]);

        $unavailableGame = factory(Game::class)->state('test')->create();
        $unavailableDistributor = factory(Distributor::class)->state('test')->create();
        $unavailableKey = factory(Key::class)->state('test')->create(['game_id' => $unavailableGame, 'distributor_id' => $unavailableDistributor]);
        $purchase = factory(Payment::class)->state('test')->create(['key_id' => $unavailableKey]);

        $this->assertTrue(Game::availableAtDistributor($availableDistributor->slug)->get()->contains($availableGame));
        $this->assertFalse(Game::availableAtDistributor($unavailableDistributor->slug)->get()->contains($unavailableGame));
    }

    /** @test */
    public function it_is_not_available_for_sale_having_none_available_keys()
    {
        $this->assertFalse($this->game->isAvailable());

        factory(Key::class)->state('test')->create(['game_id' => $this->game]);

        $this->assertTrue($this->game->fresh()->isAvailable());
    }

    /** @test */
    public function it_knows_its_first_available_key_at_the_specified_distributor()
    {
        $distributor = factory(Distributor::class)->state('test')->create();
        $availableKey = factory(Key::class)->state('test')->create(['game_id' => $this->game, 'distributor_id' => $distributor]);

        $unavailableKey = factory(Key::class)->state('test')->create(['game_id' => $this->game, 'distributor_id' => $distributor]);
        $purchase = factory(Payment::class)->state('test')->create(['key_id' => $unavailableKey]);

        $this->assertTrue($this->game->getFirstAvailableKeyAtDistributor($distributor->slug)->is($availableKey));
    }
}
