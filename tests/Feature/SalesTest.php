<?php

namespace Tests\Feature;

use App\Distributor;
use App\Game;
use App\Key;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SalesTest extends TestCase
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
    public function a_seller_can_sell_keys_for_the_specific_game()
    {
        $game = factory(Game::class)->create();
        $distributor = factory(Distributor::class)->create();

        $attributes = ['distributor' => $distributor->slug, 'keys' => 'HGSD-235A-HSDH-HKS9'];

        $response = $this->actingAs($this->seller, 'api')->post(
            route('sales.store', compact('game')),
            $attributes
        );

    }
}
