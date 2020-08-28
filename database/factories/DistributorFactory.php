<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Distributor;
use App\Platform;
use Faker\Generator as Faker;

$factory->define(Distributor::class, function (Faker $faker) {
    return [
        'name' => $faker->unique()->word,
    ];
});

$factory->state(Distributor::class, 'test', function (Faker $faker) {
    return [
        'platform_id' => factory(Platform::class),
    ];
});
