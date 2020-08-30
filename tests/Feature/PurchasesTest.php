<?php

namespace Tests\Feature;

use App\Distributor;
use App\Game;
use App\Key;
use App\Payment;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PurchasesTest extends TestCase
{
    protected $seller;
    protected $buyer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seller = factory(User::class)->state('seller')->create();
        $this->buyer = factory(User::class)->state('buyer')->create();
    }

    /** @test */
    public function an_unauthorized_user_can_not_initialize_a_purchase()
    {
        $key = factory(Key::class)->state('test')->create();

        $this->post(
            route('purchases.store', ['game' => $key->game, 'distributor' => $key->distributor])
        )->assertRedirect(route('login'));

        $seller = factory(User::class)->state('seller')->create();
        $this->actingAs($seller, 'api')->post(
            route('purchases.store', ['game' => $key->game, 'distributor' => $key->distributor])
        )->assertForbidden();
    }

    /** @test */
    public function a_buyer_can_initialize_purchasing_a_key_for_the_specific_game_at_the_specific_distributor_and_it_makes_key_reserved()
    {
        $key = factory(Key::class)->state('test')->create();
        $this->assertFalse($key->isReservedBy($this->buyer));

        $this->actingAs($this->buyer, 'api')->post(
            route('purchases.store', ['game' => $key->game, 'distributor' => $key->distributor])
        );
        $this->assertTrue($key->fresh()->isReservedBy($this->buyer));
    }

    /** @test */
    public function a_user_can_confirm_initialized_purchase_which_makes_key_unavailable_for_next_purchase()
    {
        $key = factory(Key::class)->state('test')->create();
        $purchase = factory(Payment::class)->state('test')->create(['key_id' => $key, 'confirmed_at' => null]);
        $attributes = ['card' => 4242424242424242, 'token' => $purchase->token];

        $this->actingAs($purchase->payer, 'api')->post(route('payments.confirm'), $attributes);
        $this->assertFalse($key->isAvailable());
    }
}
