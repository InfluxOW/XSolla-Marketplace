<?php

namespace Tests\Unit;

use App\Distributor;
use App\Game;
use App\Platform;
use Tests\TestCase;

class PlatformTest extends TestCase
{
    protected $platform;

    protected function setUp(): void
    {
        parent::setUp();

        $this->platform = factory(Platform::class)->create();
    }

    /** @test */
    public function it_has_games()
    {
        $game = factory(Game::class)->state('test')->create(['platform_id' => $this->platform]);

        $this->assertInstanceOf(Game::class, $this->platform->games->first());
        $this->assertTrue($this->platform->games->contains($game));
    }

    /** @test */
    public function it_has_distributors()
    {
        $distributor = factory(Distributor::class)->state('test')->create(['platform_id' => $this->platform]);

        $this->assertInstanceOf(Distributor::class, $this->platform->distributors->first());
        $this->assertTrue($this->platform->distributors->contains($distributor));
    }
}
