<?php

namespace Tests\Feature;

use App\Distributor;
use App\Game;
use App\Key;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PurchasesTest extends TestCase
{
    protected $seller;
    protected $buyer;

    protected function setUp():void
    {
        parent::setUp();

        $this->seller = factory(User::class)->state('seller')->create();
        $this->buyer = factory(User::class)->state('buyer')->create();
    }

    /** @test */
    public function a_buyer_can_buy_keys_for_the_specific_game()
    {
        $game = factory(Game::class)->state('test')->create();
        $distributor = factory(Distributor::class)->state('test')->create();
        $key = factory(Key::class)->state('test')->create(['game_id' => $game, 'distributor_id' => $distributor]);

        $response = $this->actingAs($this->buyer, 'api')->post(
            route('purchases.store', ['game' => $game, 'distributor' => $distributor])
        );
        dd($response->content());
    }
}
