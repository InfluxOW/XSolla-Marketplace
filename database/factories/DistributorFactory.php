<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Distributor;
use Faker\Generator as Faker;

$factory->define(Distributor::class, function (Faker $faker) {
    return [
        'name' => $faker->word,
    ];
});

$factory->state(Distributor::class, 'steam', function() {
    return [
        'name' => 'Steam',
    ];
});

$factory->state(Distributor::class, 'gog', function() {
    return [
        'name' => 'GOG',
    ];
});

$factory->state(Distributor::class, 'ps store', function() {
    return [
        'name' => 'Playstation Store',
    ];
});

$factory->state(Distributor::class, 'egs', function() {
    return [
        'name' => 'Epic Games Store',
    ];
});
