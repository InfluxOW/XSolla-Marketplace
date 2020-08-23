<?php /** @noinspection StaticInvocationViaThisInspection */

namespace Tests\Feature;

use App\Distributor;
use App\Game;
use App\Key;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DistributorsTest extends TestCase
{
    protected $distributors;

    /** @test */
    public function user_can_fetch_all_distributors()
    {
        $distributors = factory(Distributor::class, 3)->create();

        $this->get(route('distributors.index'))
            ->assertOk()
            ->assertJsonCount($distributors->count(), 'data');
    }

    /** @test */
    public function user_can_fetch_all_games_available_at_the_specific_distributor()
    {
        $distributor = factory(Distributor::class)->create();
        $keysWithGames = factory(Key::class, 5)->state('test')->create(['distributor_id' => $distributor->id]);

        $this->get(route('distributors.show', compact('distributor')))
            ->assertOk()
            ->assertJsonCount($distributor->games_count, 'data');
    }

    /** @test */
    public function if_game_is_unavailable_it_wont_be_shown_at_the_specific_distributor()
    {
        $distributor = factory(Distributor::class)->create();
        $availableGame = factory(Key::class)->state('test')->create()->game;
        $unavailableGame = factory(Game::class)->create();

        $this->get(route('distributors.show', compact('distributor')))
            ->assertOk()
            ->assertJsonCount($distributor->games_count, 'data');
    }
}
