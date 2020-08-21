<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Product;
use App\Purchase;
use App\User;
use Faker\Generator as Faker;

$factory->define(Purchase::class, function (Faker $faker) {
    return [
        'made_at' => $faker->dateTimeBetween('-3 months', 'now'),
    ];
});

$factory->afterMaking(Purchase::class, function (Purchase $purchase) {
    $product = Product::available()->inRandomOrder()->take(1)->first();
    $buyer = User::buyer()->inRandomOrder()->take(1)->first();

    $purchase->product()->associate($product);
    $purchase->buyer()->associate($buyer);
    $purchase->save();
});
