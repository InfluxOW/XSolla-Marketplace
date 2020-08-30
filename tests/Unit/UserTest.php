<?php

namespace Tests\Unit;

use App\Events\PaymentConfirmed;
use App\Key;
use App\Payment;
use App\User;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class UserTest extends TestCase
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
    public function it_has_payments()
    {
        $purchase = factory(Payment::class)->state('test')->create(['payer_id' => $this->buyer]);

        $this->assertInstanceOf(Payment::class, $this->buyer->payments->first());
        $this->assertTrue($this->buyer->payments->contains($purchase));
    }

    /** @test */
    public function it_has_keys()
    {
        $key = factory(Key::class)->state('test')->create(['owner_id' => $this->seller]);

        $this->assertInstanceOf(Key::class, $this->seller->keys->first());
        $this->assertTrue($this->seller->keys->contains($key));
    }

    /** @test */
    public function it_has_sales_through_its_keys()
    {
        $key = factory(Key::class)->state('test')->create(['owner_id' => $this->seller]);
        $sale = factory(Payment::class)->state('test')->create(['payer_id' => $this->buyer, 'key_id' => $key]);

        $this->assertInstanceOf(Payment::class, $this->seller->sales->first());
        $this->assertTrue($this->seller->sales->contains($sale));
    }
}
