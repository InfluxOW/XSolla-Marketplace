<?php /** @noinspection StaticInvocationViaThisInspection */

namespace Tests\Feature;

use App\Distributor;
use App\Game;
use App\Key;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DistributorsTest extends TestCase
{
    /** @test */
    public function user_can_fetch_all_distributors()
    {
        $distributors = factory(Distributor::class, 3)->state('test')->create();

        $this->get(route('distributors.index'))
            ->assertOk()
            ->assertJsonCount($distributors->count(), 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => ['name']
                ]
            ]);
    }
}
