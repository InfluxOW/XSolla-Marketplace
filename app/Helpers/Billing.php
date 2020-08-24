<?php

namespace App\Helpers;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Billing
{
    public static function generatePaymentSession(Model $product, User $user, int $price): string
    {
        $timestamp = now()->timestamp;

        return bcrypt("{$product->id}/{$user->email}/{$price}/{$timestamp}");
    }
}
