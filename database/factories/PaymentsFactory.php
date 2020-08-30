<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Key;
use App\Payment;
use App\User;
use Faker\Generator as Faker;

$factory->define(Payment::class, function (Faker $faker) {
    return [
        'reserved_until' => now()->addHour(),
        'confirmed_at' => $faker->dateTimeBetween('-3 months', 'now'),
        'token' => $faker->uuid
    ];
});

$factory->state(Payment::class, 'test', function (Faker $faker) {
    return [
        'key_id' => factory(Key::class)->state('test'),
        'payer_id' => factory(User::class)->state('buyer'),
    ];
});

$factory->afterMaking(Payment::class, function (Payment $purchase) {
    if (app('env') === 'local') {
        $key = Key::available()->inRandomOrder()->take(1)->first();
        $buyer = User::buyer()->inRandomOrder()->take(1)->first();

        $purchase->key()->associate($key);
        $purchase->payer()->associate($buyer);
        $purchase->save();
    }
});
