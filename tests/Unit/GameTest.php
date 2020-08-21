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
}
