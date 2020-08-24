<?php

namespace App\Helpers;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Billing
{
    public static function generatePaymentToken(Model $product, string $email, int $price): string
    {
        $timestamp = now()->timestamp;
        $key = $product->getKey();
        $class = $product->getMorphClass();

        return bcrypt("{$key}/{$class}/{$email}/{$price}/{$timestamp}");
    }
}
