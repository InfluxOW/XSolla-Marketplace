<?php

namespace Tests\Feature;

use App\Distributor;
use App\Game;
use App\Key;
use App\Platform;
use App\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SalesTest extends TestCase
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
    public function an_unauthorized_user_can_not_sell_keys()
    {
        $platform = factory(Platform::class)->create();
        $game = factory(Game::class)->state('test')->create(['platform_id' => $platform]);
        $distributor = factory(Distributor::class)->state('test')->create(['platform_id' => $platform]);

        $this->post(route('sales.store', compact('game', 'distributor')), [])
            ->assertRedirect(route('login'));

        $this->actingAs($this->buyer, 'api')->post(
            route('sales.store', compact('game', 'distributor')),
            []
        )->assertForbidden();
    }

    /** @test */
    public function a_seller_can_sell_keys_for_the_specific_game()
    {
        $platform = factory(Platform::class)->create();
        $game = factory(Game::class)->state('test')->create(['platform_id' => $platform]);
        $distributor = factory(Distributor::class)->state('test')->create(['platform_id' => $platform]);

        $serialNumber = 'HGSD-235A-HSDH-HKS9';
        $attributes = ['keys' => $serialNumber];

        $this->actingAs($this->seller, 'api')->post(
            route('sales.store', compact('game', 'distributor')),
            $attributes
        )->assertCreated();

        $this->assertDatabaseHas('keys', ['serial_number' => $serialNumber]);
    }

    /** @test */
    public function it_throws_an_exception_if_specified_distributor_belongs_to_another_platform_than_game_does()
    {
        $this->withoutExceptionHandling();

        $pc = factory(Platform::class)->create(['name' => 'PC']);
        $ps4 = factory(Platform::class)->create(['name' => 'PS4']);
        $game = factory(Game::class)->state('test')->create(['platform_id' => $pc]);
        $distributor = factory(Distributor::class)->state('test')->create(['platform_id' => $ps4]);

        $this->expectException(ModelNotFoundException::class);
        $this->actingAs($this->seller, 'api')->post(
            route('sales.store', compact('game', 'distributor')),
            []
        );
    }
}
