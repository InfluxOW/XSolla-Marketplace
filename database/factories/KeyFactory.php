<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Key;
use App\Game;
use App\Distributor;
use Faker\Generator as Faker;

$factory->define(Key::class, function (Faker $faker) {
    return [
        'serial_number' => $faker->uuid
    ];
});

$factory->afterMaking(Key::class, function (Key $key) {
    $game = Game::inRandomOrder()->take(1)->first();
    $distributionService = Distributor::inRandomOrder()->take(1)->first();

    $key->game()->associate($game);
    $key->distributor()->associate($distributionService);
    $key->save();
});
