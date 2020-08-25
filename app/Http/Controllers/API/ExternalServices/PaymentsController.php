<?php

namespace App\Http\Controllers\API\ExternalServices;

use App\Http\Controllers\Controller;
use App\Http\Requests\BillingRequest;

class PaymentsController extends Controller
{
    public function confirm(BillingRequest $request)
    {
        $purchase = Purchase::where('payment_session_token', $request->payment_session_token)->first();
        $request->user()->confirmPurchase($purchase);

        return "Your payment is successfully proceeded! Key has been to your email.";
    }
}
