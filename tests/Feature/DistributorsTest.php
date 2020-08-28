<?php

/** @noinspection StaticInvocationViaThisInspection */

namespace Tests\Feature;

use App\Distributor;
use App\Game;
use App\Key;
use App\Platform;
use App\Purchase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Tests\TestCase;

class DistributorsTest extends TestCase
{
    /** @test */
    public function user_can_fetch_all_distributors()
    {
        $distributors = factory(Distributor::class, 3)->state('test')->create();

        $this->get(route('distributors.index'))
            ->assertOk()
            ->assertJsonCount($distributors->count(), 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => ['name', 'platform']
                ]
            ]);
    }

    /** @test */
    public function it_shows_a_total_number_of_games_and_number_of_available_games_for_every_distributor()
    {
        $pc = factory(Platform::class)->create(['name' => 'PC']);
        $ps4 = factory(Platform::class)->create(['name' => 'PS4']);

        $steam = factory(Distributor::class)->create(['platform_id' => $pc, 'name' => 'Steam']);
        $psStore = factory(Distributor::class)->create(['platform_id' => $ps4, 'name' => 'PlayStation Store']);

        $pcGame = factory(Game::class)->state('test')->create(['platform_id' => $pc]);
        $ps4Games = factory(Game::class, 2)->state('test')->create(['platform_id' => $ps4]);

        $csgo = factory(Key::class)->state('test')->create(['distributor_id' => $steam, 'game_id' => $pcGame]);
        $horizon = factory(Key::class)->state('test')->create(['distributor_id' => $psStore, 'game_id' => $ps4Games->first()]);
        $godOfWar = factory(Key::class)->state('test')->create(['distributor_id' => $psStore, 'game_id' => $ps4Games->second()]);
        $purchase = factory(Purchase::class)->state('test')->create(['key_id' => $horizon]);

        $distributors = $this->get(route('distributors.index'))
            ->assertOk()
            ->json('data');

        $this->assertEquals(
            array_column($distributors, 'total_games', 'name'),
            [$steam->name => $steam->games->count(), $psStore->name => $psStore->games->count()]
        );
        $this->assertEquals(
            array_column($distributors, 'available_games', 'name'),
            [$steam->name => $steam->games->filter->isAvailable()->count(), $psStore->name => $psStore->games->filter->isAvailable()->count()]
        );
    }
}
