<?php

namespace Tests\Unit;

use App\Game;
use App\Key;
use App\Purchase;
use Tests\TestCase;

class GameTest extends TestCase
{
    protected $game;

    protected function setUp():void
    {
        parent::setUp();

        $this->game = factory(Game::class)->create();
    }

    /** @test */
    public function it_has_keys()
    {
        $key = factory(Key::class)->state('test')->create(['game_id' => $this->game]);

        $this->assertInstanceOf(Key::class, $this->game->keys->first());
        $this->assertTrue($this->game->keys->contains($key));
    }

    /** @test */
    public function it_has_sales_through_its_keys()
    {
        $key = factory(Key::class)->state('test')->create(['game_id' => $this->game]);
        $purchase = factory(Purchase::class)->state('test')->create(['key_id' => $key]);

        $this->assertInstanceOf(Purchase::class, $this->game->sales->first());
        $this->assertTrue($this->game->sales->contains($purchase));
    }

    /** @test */
    public function it_can_be_scoped_to_only_available_for_purchase_games()
    {
        $availableGame = $this->game;
        $availableKey = factory(Key::class)->state('test')->create(['game_id' => $availableGame]);

        $unavailableGame = factory(Game::class)->create();
        $unavailableKey = factory(Key::class)->state('test')->create(['game_id' => $unavailableGame]);
        $purchase = factory(Purchase::class)->state('test')->create(['key_id' => $unavailableKey]);

        $this->assertTrue(Game::available()->get()->contains($availableGame));
        $this->assertFalse(Game::available()->get()->contains($unavailableGame));
    }

    /** @test */
    public function it_knows_if_it_is_available_for_sale()
    {
        $this->assertFalse($this->game->isAvailable());

        factory(Key::class)->state('test')->create(['game_id' => $this->game]);

        $this->assertTrue($this->game->fresh()->isAvailable());
    }
}
