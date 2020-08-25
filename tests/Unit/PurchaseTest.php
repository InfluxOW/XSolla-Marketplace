<?php

namespace Tests\Unit;

use App\Key;
use App\Purchase;
use App\User;
use Tests\TestCase;

class PurchaseTest extends TestCase
{
    protected $purchase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->purchase = factory(Purchase::class)->state('test')->create(['confirmed_at' => null]);
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
    public function it_has_a_seller()
    {
        $this->assertInstanceOf(User::class, $this->purchase->seller);
    }

    /** @test */
    public function it_knows_if_it_is_confirmed_or_not()
    {
        $this->assertFalse($this->purchase->isConfirmed());
        $this->assertTrue($this->purchase->isUnconfirmed());

        $this->purchase->confirm();

        $this->assertTrue($this->purchase->isConfirmed());
        $this->assertFalse($this->purchase->isUnconfirmed());
    }
}
