<?php

namespace App\Http\Controllers\API\ExternalServices;

use App\Http\Controllers\Controller;
use App\Http\Requests\BillingRequest;
use App\Payment;

class PaymentsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api']);
    }

    /**
     * @OA\Post(
     * path="/payments/confirm",
     * summary="Billing Provider",
     * description="Confirm a purchase",
     * operationId="paymentsConfirm",
     * tags={"Fake External Services"},
     * security={
     *   {"access_token": {}},
     * },
     * @OA\RequestBody(
     *    required=true,
     *    description="Payment data",
     *    @OA\JsonContent(
     *       required={"token","card"},
     *       @OA\Property(property="token", type="string", example="$2y$04$1rS6a2vh9ePY.mjg0I1gTeCskbl/jTy65DOTDDY/P6n4yvL3J4LcK"),
     *       @OA\Property(property="card", type="integer", example="4425669844123325"),
     *    ),
     * ),
     *  @OA\Response(
     *      response=200,
     *      description="Your payment is successfully proceeded! Key has been to your email.",
     *  ),
     *  @OA\Response(
     *      response=401,
     *      description="You should be authorized to access the endpoint",
     *  ),
     *  @OA\Response(
     *      response=403,
     *      description="You don't have permissions to access the endpoint",
     *  ),
     * )
     * )
     * @param \App\Http\Requests\BillingRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function confirm(BillingRequest $request)
    {
        $purchase = Payment::unconfirmed()->where('token', $request->token)->first();

        if (is_null($purchase)) {
            return response("You motherfucker, come on you little ass… fuck with me, eh? You fucking little asshole, dickhead cocksucker…You fuckin' come on, come fuck with me! I'll get your ass, you jerk! Oh, you fuckhead motherfucker! Fuck all you and your family! Come on, you cocksucker, slime bucket, shitface turdball! Come on, you scum sucker, you fucking with me? Come on, you asshole!!!", 404);
        }
        $purchase->confirm();

        return response("Your payment is successfully proceeded! Key has been to your email.", 200);
    }
}
