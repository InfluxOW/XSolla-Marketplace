<?php

namespace App\Http\Controllers\API;

use App\Exceptions\NoAvailableKeysException;
use App\Game;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PurchasesController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api', 'buyer']);
    }

    /**
     * @OA\Post(
     * path="/games/{game:slug}/{distributor:slug}/purchase",
     * summary="Purchase a key",
     * description="Purchase a key for the specific game at the specific distributor",
     * operationId="purchaseStore",
     * tags={"Purchases"},
     * security={
     *   {"access_token": {}},
     * },
     *   @OA\Parameter(
     *      name="game:slug",
     *      in="path",
     *      required=true,
     *      @OA\Schema(
     *           type="string"
     *      )
     *   ),
     *   @OA\Parameter(
     *      name="distributor:slug",
     *      in="path",
     *      required=true,
     *      @OA\Schema(
     *           type="string"
     *      )
     *   ),
     * @OA\Response(
     *    response=201,
     *    description="Keys has been reserved",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="You have successfully reserved a key for the game 'The Witcher 3: Wild Hunt' at Steam. To initialize payment send HTTP POST request with your card credentials and payment session token to the billing provider."),
     *       @OA\Property(property="payment_session_token", type="string", example="$2y$04$1rS6a2vh9ePY.mjg0I1gTeCskbl/jTy65DOTDDY/P6n4yvL3J4LcK"),
     * )
     *  ),
     * @OA\Response(
     *     response=422,
     *     description="No available keys",
     *     @OA\JsonContent(
     *        @OA\Items(type="string", example="Selected game has no available keys at the specified distributor."),
     *     )
     *  ),
     *  @OA\Response(
     *      response=401,
     *      description="You should be authorized to access the endpoint",
     *  ),
     *  @OA\Response(
     *      response=403,
     *      description="You don't have permissions to access the endpoint",
     *  ),
     * @OA\Response(
     *      response=404,
     *      description="Specified game or distributor was not been found"
     *   ),
     * )
     * )
     * @param \Illuminate\Http\Request $request
     * @param \App\Game $game
     * @param $distributor
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function store(Request $request, Game $game, $distributor)
    {
        try {
            $key = $game->getFirstAvailableKeyAtDistributor($distributor);
        } catch (NoAvailableKeysException $e) {
            return response($e->getMessage(), 422);
        }

        $purchase = $request->user()->reserve($key);

        return response(
            [
                'message' => "You have successfully reserved a key for the game '{$game->name}' at {$purchase->key->distributor->name}. To initialize payment send HTTP POST request with your card credentials and payment session token to the billing provider.",
                'payment_session_token' => $purchase['payment_session_token']
            ],
            201
        );
    }
}
