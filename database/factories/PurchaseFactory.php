<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Key;
use App\Purchase;
use App\User;
use Faker\Generator as Faker;

$factory->define(Purchase::class, function (Faker $faker) {
    return [
        'confirmed_at' => $faker->dateTimeBetween('-3 months', 'now'),
        'payment_session_token' => $faker->uuid
    ];
});

$factory->state(Purchase::class, 'test', function (Faker $faker) {
    return [
        'key_id' => factory(Key::class)->state('test'),
        'buyer_id' => factory(User::class)->state('buyer'),
    ];
});

$factory->afterMaking(Purchase::class, function (Purchase $purchase) {
    if (app('env') === 'local') {
        $key = Key::available()->inRandomOrder()->take(1)->first();
        $buyer = User::buyer()->inRandomOrder()->take(1)->first();

        $purchase->key()->associate($key);
        $purchase->buyer()->associate($buyer);
        $purchase->save();
    }
});

