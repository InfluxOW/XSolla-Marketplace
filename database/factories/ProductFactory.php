<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Key;
use App\Product;
use App\User;
use Faker\Generator as Faker;

$factory->define(Product::class, function (Faker $faker) {
    return [
        'created_at' => $faker->dateTimeBetween('-1 year', '-3 months'),
    ];
});

$factory->afterMaking(Product::class, function (Product $product) {
    $key = Key::available()->inRandomOrder()->take(1)->first();
    $seller = User::seller()->inRandomOrder()->take(1)->first();

    $product->key()->associate($key);
    $product->seller()->associate($seller);
    $product->save();
});
