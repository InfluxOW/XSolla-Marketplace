<?php

namespace Tests\Feature;

use App\Distributor;
use App\Game;
use App\Key;
use App\Platform;
use App\Payment;
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
    public function it_shows_a_number_of_available_keys_for_the_game_for_an_every_distributor()
    {
        $this->withoutExceptionHandling();
        $game = $this->games->first();
        $steam = factory(Distributor::class)->create(['name' => 'Steam', 'platform_id' => $game->platform]);
        $gog = factory(Distributor::class)->create(['name' => 'GOG', 'platform_id' => $game->platform]);

        $keysAtSteam = factory(Key::class, 3)->state('test')->create(['game_id' => $game, 'distributor_id' => $steam]);
        $keysAtGog = factory(Key::class, 2)->state('test')->create(['game_id' => $game, 'distributor_id' => $gog]);
        $purchase = factory(Payment::class)->state('test')->create(['key_id' => $keysAtSteam->first()]);

        $game = $this->get(route('games.show', ['game' => $game]))
            ->assertOk()
            ->json('data');

        $this->assertEquals(
            $game['available_keys'],
            ['Steam' => $keysAtSteam->filter->isAvailable()->count(), 'GOG' => $keysAtGog->filter->isAvailable()->count()]
        );
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
    public function game_can_not_be_stored_twice()
    {
        $seller = factory(User::class)->state('seller')->create();
        $game = factory(Game::class)->state('test')->create();

        $this->actingAs($seller, 'api')
            ->post(route('games.store'), ['name' => $game->name, 'price' => $game->price, 'platform' => $game->platform->slug])
            ->assertRedirect();
        $this->assertDatabaseCount('games', $this->games->count() + 1);
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
