<?php

namespace App\Http\Controllers\API\ExternalServices;

use App\Http\Controllers\Controller;
use App\Http\Requests\BillingRequest;
use App\Purchase;

class PaymentsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api']);
    }

    public function confirm(BillingRequest $request)
    {
        $purchase = Purchase::unconfirmed()->where('payment_session_token', $request->payment_session_token)->first();

        if (is_null($purchase)) {
            return "You motherfucker, come on you little ass… fuck with me, eh? You fucking little asshole, dickhead cocksucker…You fuckin' come on, come fuck with me! I'll get your ass, you jerk! Oh, you fuckhead motherfucker! Fuck all you and your family! Come on, you cocksucker, slime bucket, shitface turdball! Come on, you scum sucker, you fucking with me? Come on, you asshole!!!";
        }

        $purchase->confirm();
        return "Your payment is successfully proceeded! Key has been to your email.";
    }
}
