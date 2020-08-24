<?php

namespace Tests\Feature;

use App\Game;
use App\Key;
use App\Purchase;
use App\User;
use DistributorSeeder;
use Tests\TestCase;

class GamesQueriesTest extends TestCase
{
    protected function setUp():void
    {
        parent::setUp();
        $this->seedData();
    }

    /** @test */
    public function a_user_can_fetch_all_games_below_specific_price()
    {
        $this->get(route('games.index', ['filter[price_lte]' => 35]))
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }

    /** @test */
    public function a_user_can_fetch_all_games_above_specific_price()
    {
        $this->get(route('games.index', ['filter[price_gte]' => 35]))
            ->assertOk()
            ->assertJsonCount(3, 'data');
    }

    /** @test */
    public function a_user_can_fetch_all_games_with_specific_name()
    {
        $this->get(route('games.index', ['filter[name]' => 'GTA']))
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }

    /** @test */
    public function a_user_can_fetch_all_games_within_specific_platform()
    {
        $this->get(route('games.index', ['filter[platform]' => 'pc']))
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }

    /** @test */
    public function a_user_can_fetch_all_games_within_specific_distributor_even_if_it_has_no_available_keys()
    {
        $this->get(route('games.index', ['filter[distributor]' => 'steam']))
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }

    /** @test */
    public function a_user_can_fetch_all_games_that_have_available_keys()
    {
        $this->get(route('games.index', ['filter[available]' => true]))
            ->assertOk()
            ->assertJsonCount(3, 'data');
    }

    /** @test */
    public function a_user_can_fetch_all_games_that_have_available_keys_at_specific_distributor()
    {
        $this->get(route('games.index', ['filter[available_at]' => 'steam']))
            ->assertOk()
            ->assertJsonCount(0, 'data');

        $this->get(route('games.index', ['filter[available_at]' => 'uplay']))
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }

    /** @test */
    public function a_user_can_fetch_all_games_sorted_by_price_asc()
    {
        $games = $this->get(route('games.index', ['sort' => 'price']))
            ->assertOk()
            ->json('data');
        $this->assertEquals([30, 40, 50, 60], array_column($games, 'price'));
    }

    /** @test */
    public function a_user_can_fetch_all_games_sorted_by_price_desc()
    {
        $games = $this->get(route('games.index', ['sort' => '-price']))
            ->assertOk()
            ->json('data');
        $this->assertEquals([60, 50, 40, 30], array_column($games, 'price'));
    }

    /** @test */
    public function a_user_can_fetch_all_games_sorted_by_platform_id_asc()
    {
        $games = $this->get(route('games.index', ['sort' => 'platform']))
            ->assertOk()
            ->json('data');
        $this->assertEquals(['PlayStation 4', 'Xbox One', 'PC', 'Nintendo Switch'], array_column($games, 'platform'));
    }

    /** @test */
    public function a_user_can_fetch_all_games_sorted_by_platform_id_desc()
    {
        $games = $this->get(route('games.index', ['sort' => '-platform']))
            ->assertOk()
            ->json('data');
        $this->assertEquals(['Nintendo Switch', 'PC', 'Xbox One', 'PlayStation 4'], array_column($games, 'platform'));
    }

    /** @test */
    public function a_user_can_fetch_all_games_sorted_by_name_asc()
    {
        $games = $this->get(route('games.index', ['sort' => 'name']))
            ->assertOk()
            ->json('data');
        $this->assertEquals(['CS:GO', 'GTA V', 'Horizon: Zero Dawn', 'Paper Mario'], array_column($games, 'name'));
    }

    /** @test */
    public function a_user_can_fetch_all_games_sorted_by_name_desc()
    {
        $games = $this->get(route('games.index', ['sort' => '-name']))
            ->assertOk()
            ->json('data');
        $this->assertEquals(['Paper Mario', 'Horizon: Zero Dawn', 'GTA V', 'CS:GO'], array_column($games, 'name'));
    }

    protected function seedData()
    {
        /*
         * Platforms and distributors
         * */

        $this->seed([
            DistributorSeeder::class,
        ]);

        /*
         * Games
         * */
        $horizon = factory(Game::class)->create(['name' => 'Horizon: Zero Dawn', 'price' => 50, 'platform_id' => 1, 'description' => null]);
        $gta = factory(Game::class)->create(['name' => 'GTA V', 'price' => 60, 'platform_id' => 2]);
        $csgo = factory(Game::class)->create(['name' => 'CS:GO', 'price' => 30, 'platform_id' => 3]);
        $mario = factory(Game::class)->create(['name' => 'Paper Mario', 'price' => 40, 'platform_id' => 4]);

        /*
         * Users
         * */
        $seller = factory(User::class)->state('seller')->create();
        $buyer = factory(User::class)->state('buyer')->create();

        /*
         * Keys
         * */
        factory(Key::class, 3)->create(['owner_id' => $seller, 'game_id' => $horizon, 'distributor_id' => 1]);
        factory(Key::class, 4)->create(['owner_id' => $seller, 'game_id' => $gta, 'distributor_id' => 2]);
        factory(Key::class, 1)->create(['owner_id' => $seller, 'game_id' => $csgo, 'distributor_id' => 3]);
        factory(Key::class, 2)->create(['owner_id' => $seller, 'game_id' => $csgo, 'distributor_id' => 4]);
        factory(Key::class, 4)->create(['owner_id' => $seller, 'game_id' => $csgo, 'distributor_id' => 5]);
        factory(Key::class, 3)->create(['owner_id' => $seller, 'game_id' => $csgo, 'distributor_id' => 6]);
        factory(Key::class, 1)->create(['owner_id' => $seller, 'game_id' => $mario, 'distributor_id' => 7]);

        /*
         * Purchases
         * */
        factory(Purchase::class)->create(['buyer_id' => $buyer, 'key_id' => 1]);
        factory(Purchase::class)->create(['buyer_id' => $buyer, 'key_id' => 8]);
        factory(Purchase::class)->create(['buyer_id' => $buyer, 'key_id' => 11]);
        factory(Purchase::class)->create(['buyer_id' => $buyer, 'key_id' => 15]);
        factory(Purchase::class)->create(['buyer_id' => $buyer, 'key_id' => 18]);
    }
}
