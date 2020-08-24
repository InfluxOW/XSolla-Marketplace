<?php

namespace Tests\Feature;

use App\Game;
use App\Platform;
use App\User;
use Illuminate\Support\Arr;
use Tests\TestCase;

class GamesTest extends TestCase
{
    protected $games;
    protected $attributes;

    protected function setUp(): void
    {
        parent::setUp();

        $this->games = factory(Game::class, 3)->state('test')->create();
        $this->attributes = ['name' => 'Test Game', 'price' => 50, 'platform' => Platform::first()->slug];
    }

    /** @test */
    public function a_user_can_fetch_all_games()
    {
        $this->get(route('games.index'))
            ->assertOk()
            ->assertJsonCount($this->games->count(), 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => ['name', 'description', 'price']
                ]
            ]);
    }

    /** @test */
    public function a_user_can_fetch_specific_game()
    {
        $this->get(route('games.show', ['game' => $this->games->first()]))
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'name', 'description', 'price'
                ]
            ]);
    }

    /** @test */
    public function a_seller_can_store_a_new_game()
    {
        $seller = factory(User::class)->state('seller')->create();

        $this->actingAs($seller, 'api')
            ->post(route('games.store'), $this->attributes)
            ->assertRedirect();
        $this->assertDatabaseHas('games', Arr::only($this->attributes, ['name', 'price']));
    }

    /** @test */
    public function an_unauthorized_user_can_not_store_a_new_game()
    {
        $this->post(route('games.store'), [])
            ->assertRedirect(route('login'));

        $buyer = factory(User::class)->state('buyer')->create();
        $this->actingAs($buyer, 'api')
            ->post(route('games.store'), [])
            ->assertForbidden();
    }
}
