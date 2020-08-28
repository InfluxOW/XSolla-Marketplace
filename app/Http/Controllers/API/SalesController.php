<?php

namespace App\Http\Controllers\API;

use App\Game;
use App\Http\Controllers\Controller;
use App\Http\Requests\SalesRequest;
use App\Key;

class SalesController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api', 'seller']);
    }

    /**
     * @OA\Post(
     * path="/games/{game:slug}/{distributor:slug}/sell",
     * summary="Sell a key or keys",
     * description="Sell keys for the specific game at the specific distributor",
     * operationId="keysStore",
     * tags={"Sales"},
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
     * @OA\RequestBody(
     *    required=true,
     *    description="Keys data",
     *    @OA\JsonContent(
     *       required={"keys"},
     *       @OA\Property(property="keys", type="string[]", example={"HG5D-SXC2-KNB5-M2K2", "JSD6-SDXC-664H-9JJ2"}),
     *    ),
     * ),
     * @OA\Response(
     *    response=201,
     *    description="Keys has been put on sale",
     *    @OA\JsonContent(@OA\Items(type="string", example="You have successfully put up for sale keys for the game The Witcher 3: Wild Hunt at distributor Steam!"))
     *  ),
     * @OA\Response(
     *     response=422,
     *     description="Keys has not been stored due to validation error",
     *     @OA\JsonContent(
     *        @OA\Property(property="message", type="string", example="The given data was invalid."),
     *        @OA\Property(
     *           property="errors",
     *           type="object",
     *           @OA\Property(
     *              property="name",
     *              type="array",
     *              collectionFormat="multi",
     *              @OA\Items(
     *                 type="string",
     *                 example={"Keys attribute is required."},
     *              )
     *           )
     *        )
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
     *      description="Specified game or distributor were not been found"
     *   ),
     * )
     * )
     * @param \App\Http\Requests\SalesRequest $request
     * @param \App\Game $game
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function store(SalesRequest $request, Game $game)
    {
        Key::createManyByRequest($request);

        return response("You have successfully put up for sale keys for the game '{$game->name}' at distributor {$request->distributor->name}!", 201);
    }
}
