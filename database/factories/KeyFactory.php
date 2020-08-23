<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Key;
use App\Game;
use App\Distributor;
use App\User;
use Faker\Generator as Faker;

$factory->define(Key::class, function (Faker $faker) {
    return [
        'serial_number' => $faker->uuid
    ];
});


$factory->state(Key::class, 'test', function (Faker $faker) {
    return [
        'game_id' => factory(Game::class)->state('test'),
        'distributor_id' => factory(Distributor::class)->state('test'),
        'owner_id' => factory(User::class)->state('seller'),
    ];
});

$factory->afterMaking(Key::class, function (Key $key) {
    if (app('env') === 'local') {
        $game = Game::inRandomOrder()->take(1)->first();
        $distributionService = $game->platform->distributors->random();
        $owner = User::seller()->inRandomOrder()->take(1)->first();

        $key->game()->associate($game);
        $key->distributor()->associate($distributionService);
        $key->owner()->associate($owner);
        $key->save();
    }
});
