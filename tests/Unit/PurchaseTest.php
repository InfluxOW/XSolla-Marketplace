<?php

namespace Tests\Unit;

use App\Key;
use App\Purchase;
use App\User;
use Tests\TestCase;

class PurchaseTest extends TestCase
{
    protected $purchase;

    protected function setUp():void
    {
        parent::setUp();

        $this->purchase = factory(Purchase::class)->state('test')->create();
    }

    /** @test */
    public function it_belongs_to_a_key()
    {
        $this->assertInstanceOf(Key::class, $this->purchase->key);
    }

    /** @test */
    public function it_belongs_to_a_buyer()
    {
        $this->assertInstanceOf(User::class, $this->purchase->buyer);
    }

    /** @test */
    public function it_belongs_to_a_seller()
    {
        $this->assertInstanceOf(User::class, $this->purchase->seller);
    }
}
