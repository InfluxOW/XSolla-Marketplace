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

    /** @test */
    public function a_user_can_()
    {

    }
}
