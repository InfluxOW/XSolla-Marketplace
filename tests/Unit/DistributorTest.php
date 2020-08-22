<?php /** @noinspection StaticInvocationViaThisInspection */

namespace Tests\Unit;

use App\Distributor;
use App\Game;
use App\Key;
use Tests\TestCase;

class DistributorTest extends TestCase
{
    protected $distributor;

    protected function setUp():void
    {
        parent::setUp();

        $this->distributor = factory(Distributor::class)->create();
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
        $game = factory(Game::class)->create();
        $key = factory(Key::class)->state('test')->create(['distributor_id' => $this->distributor, 'game_id' => $game]);

        $this->assertInstanceOf(Game::class, $this->distributor->games->first());
        $this->assertTrue($this->distributor->games->contains($game));
    }

    /** @test */
    public function it_knows_its_games_count_through_available_keys()
    {
        $games = factory(Game::class, 5)->create();
        $games->each(function ($game) {
            factory(Key::class)->state('test')->create(['distributor_id' => $this->distributor, 'game_id' => $game]);
        });

        $this->assertEquals($games->count(), $this->distributor->games_count);

    }
}
