<?php

namespace Tests\Feature;

use App\Game;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GamesTest extends TestCase
{
    protected $games;

    protected function setUp():void
    {
        parent::setUp();

        $this->games = factory(Game::class, 3)->state('test')->create();
    }

    /** @test */
    public function a_user_can_fetch_all_games()
    {
        $this->get(route('games.index'))
            ->assertOk()
            ->assertJsonCount($this->games->count(), 'data');
    }

//    /** @test */
//    public function user_can_fetch_all_games_available_at_the_specific_distributor()
//    {
//        $distributor = factory(Distributor::class)->state('test')->create();
//        $keysWithGames = factory(Key::class, 5)->state('test')->create(['distributor_id' => $distributor->id]);
//
//        $this->get(route('distributors.show', compact('distributor')))
//            ->assertOk()
//            ->assertJsonCount($distributor->games_count, 'data');
//    }
//
//    /** @test */
//    public function if_game_is_unavailable_it_wont_be_shown_at_the_specific_distributor()
//    {
//        $distributor = factory(Distributor::class)->state('test')->create();
//        $availableGame = factory(Key::class)->state('test')->create()->game;
//        $unavailableGame = factory(Game::class)->state('test')->create();
//
//        $this->get(route('distributors.show', compact('distributor')))
//            ->assertOk()
//            ->assertJsonCount($distributor->games_count, 'data');
//    }
}
