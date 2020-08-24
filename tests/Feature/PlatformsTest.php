<?php

namespace Tests\Feature;

use App\Platform;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PlatformsTest extends TestCase
{
    /** @test */
    public function user_can_fetch_all_platforms()
    {
        $platforms = factory(Platform::class, 3)->create();

        $this->get(route('platforms.index'))
            ->assertOk()
            ->assertJsonCount($platforms->count(), 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => ['name']
                ]
            ]);
    }
}
