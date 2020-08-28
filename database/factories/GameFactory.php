<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Game;
use App\Platform;
use Faker\Generator as Faker;

$factory->define(Game::class, function (Faker $faker) {
    return [
        'name' => $faker->unique()->words(2, true),
        'price' => $faker->numberBetween(1, 100),
        'description' => $faker->paragraph,
    ];
});

$factory->state(Game::class, 'test', function (Faker $faker) {
    return [
      'platform_id' => factory(Platform::class),
    ];
});

$factory->afterMaking(Game::class, function (Game $game) {
    if (app('env') === 'local') {
        $platform = Platform::inRandomOrder()->take(1)->first();

        $game->platform()->associate($platform);
        $game->save();
    }
});
