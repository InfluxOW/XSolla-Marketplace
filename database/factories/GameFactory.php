<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Game;
use Faker\Generator as Faker;

$factory->define(Game::class, function (Faker $faker) {
    return [
        'name' => $faker->words(2, true),
        'price' => $faker->numberBetween(1, 100),
        'description' => $faker->paragraph,
    ];
});
