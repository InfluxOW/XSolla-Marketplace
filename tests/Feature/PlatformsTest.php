<?php

namespace Tests\Feature;

use App\Game;
use App\Platform;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Tests\TestCase;

class PlatformsTest extends TestCase
{
    /** @test */
    public function user_can_fetch_all_platforms()
    {
        $platforms = factory(Platform::class, 3)->create();

        $this->get(route('platforms.index'))
            ->assertOk()
            ->assertJsonCount($platforms->count(), 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => ['name']
                ]
            ]);
    }

    /** @test */
    public function it_shows_a_total_number_of_games_for_every_platform()
    {
        $pc = factory(Platform::class)->create(['name' => 'PC']);
        $ps4 = factory(Platform::class)->create(['name' => 'PS4']);

        $gamesAtPc = factory(Game::class, 3)->state('test')->create(['platform_id' => $pc]);
        $gamesAtPs4 = factory(Game::class, 2)->state('test')->create(['platform_id' => $ps4]);

        $platforms = $this->get(route('platforms.index'))
            ->assertOk()
            ->json('data');

        $this->assertEquals(array_column($platforms, 'total_games', 'name'), ['PC' => $gamesAtPc->count(), 'PS4' => $gamesAtPs4->count()]);
    }
}
