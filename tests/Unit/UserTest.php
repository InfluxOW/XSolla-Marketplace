<?php

namespace Tests\Unit;

use App\Events\KeyPurchased;
use App\Key;
use App\Purchase;
use App\User;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class UserTest extends TestCase
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
    public function it_knows_its_role()
    {
        $this->assertTrue($this->seller->isSeller());
        $this->assertFalse($this->seller->isBuyer());

        $this->assertTrue($this->buyer->isBuyer());
        $this->assertFalse($this->buyer->isSeller());
    }

    /** @test */
    public function it_can_be_scoped_to_users_with_the_specific_role()
    {
        $this->assertTrue(User::buyer()->get()->contains($this->buyer));
        $this->assertFalse(User::buyer()->get()->contains($this->seller));

        $this->assertTrue(User::seller()->get()->contains($this->seller));
        $this->assertFalse(User::seller()->get()->contains($this->buyer));
    }

    /** @test */
    public function it_has_purchases()
    {
        $purchase = factory(Purchase::class)->state('test')->create(['buyer_id' => $this->buyer]);

        $this->assertInstanceOf(Purchase::class, $this->buyer->purchases->first());
        $this->assertTrue($this->buyer->purchases->contains($purchase));
    }

    /** @test */
    public function it_has_sales_through_its_keys()
    {
        $key = factory(Key::class)->state('test')->create(['owner_id' => $this->seller]);
        $sale = factory(Purchase::class)->state('test')->create(['buyer_id' => $this->buyer, 'key_id' => $key]);

        $this->assertInstanceOf(Purchase::class, $this->seller->sales->first());
        $this->assertTrue($this->seller->sales->contains($sale));
    }
}
