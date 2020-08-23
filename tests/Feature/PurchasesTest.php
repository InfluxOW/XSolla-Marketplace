<?php

namespace Tests\Feature;

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
        $key = factory(Key::class)->state('test')->create();
        $attributes = ['distributor' => $key->distributor->slug, 'card' => '4242424242424242'];

        $response = $this->actingAs($this->buyer, 'api')->post(
            route('purchases.store', ['game' => $key->game]),
            $attributes
        );
    }
}
