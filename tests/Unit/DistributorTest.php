<?php

/** @noinspection StaticInvocationViaThisInspection */

namespace Tests\Unit;

use App\Distributor;
use App\Game;
use App\Key;
use App\Platform;
use Tests\TestCase;

class DistributorTest extends TestCase
{
    protected $distributor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->distributor = factory(Distributor::class)->state('test')->create();
    }

    /** @test */
    public function it_belongs_to_the_platform()
    {
        $this->assertInstanceOf(Platform::class, $this->distributor->platform);
    }

    /** @test */
    public function it_has_keys()
    {
        $key = factory(Key::class)->state('test')->create(['distributor_id' => $this->distributor]);

        $this->assertInstanceOf(Key::class, $this->distributor->keys->first());
        $this->assertTrue($this->distributor->keys->contains($key));
    }

    /** @test */
    public function it_has_games_through_its_keys()
    {
        $game = factory(Game::class)->state('test')->create();
        $key = factory(Key::class)->state('test')->create(['distributor_id' => $this->distributor, 'game_id' => $game]);

        $this->assertInstanceOf(Game::class, $this->distributor->games->first());
        $this->assertTrue($this->distributor->games->contains($game));
    }
}
