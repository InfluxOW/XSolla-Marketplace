<?php

namespace App\Http\Controllers\API;

use App\Game;
use App\Http\Controllers\Controller;
use App\Http\Requests\GamesRequest;
use App\Http\Resources\GamesResource;
use App\Platform;
use App\Repositories\GameRepository;

class GamesController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api', 'seller'])->only('store');
    }

    /**
     * @OA\Get(
     * path="/games",
     * summary="Games Index",
     * description="View all games",
     * operationId="gamesIndex",
     * tags={"Games"},
     * @OA\Response(
     *    response=200,
     *    description="Games were fetched",
     *     @OA\JsonContent(
     *     @OA\Property(
     *      property="games",
     *      type="object",
     *      collectionFormat="multi",
     *       @OA\Property(
     *         property="0",
     *         type="array",
     *         collectionFormat="multi",
     *         @OA\Items(
     *           type="object",
     *           ref="#/components/schemas/Game",
     *        )
     *      ),
     *    )
     *   )
     *  ),
     * )
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        $games = GameRepository::getGamesForIndexPage();

        return GamesResource::collection($games);
    }

    /**
     * @OA\Post(
     * path="/games",
     * summary="Games Store",
     * description="Store a new game",
     * operationId="gamesStore",
     * tags={"Games"},
     * security={
     *   {"access_token": {}},
     * },
     * @OA\RequestBody(
     *    required=true,
     *    description="Game data",
     *    @OA\JsonContent(
     *       required={"name","price", "platform"},
     *       @OA\Property(property="name", type="string", example="The Witcher 3: Wild Hunt"),
     *       @OA\Property(property="description", type="string", example="The Witcher 3: Wild Hunt is a 2015 action role-playing game developed and published by Polish developer CD Projekt Red and is based on The Witcher series of fantasy novels by Andrzej Sapkowski. It is the sequel to the 2011 game The Witcher 2: Assassins of Kings and the third main installment in the The Witcher's video game series, played in an open world with a third-person perspective."),
     *       @OA\Property(property="price", type="integer", example="50"),
     *       @OA\Property(property="platform", type="string", example="PC"),
     *    ),
     * ),
     * @OA\Response(
     *    response=302,
     *    description="Game has been stored",
     *    @OA\JsonContent(
     *       @OA\Items(type="object", ref="#/components/schemas/Game"),
     *    )
     *     ),
     * @OA\Response(
     *     response=422,
     *     description="Game has not been stored due to validation error",
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
     *                 example={"Name attribute is reqired."},
     *              )
     *           )
     *        )
     *     )
     *  )
     * )
     * @param \App\Http\Requests\GamesRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(GamesRequest $request)
    {
        $platform = Platform::whereName($request->platform)->first();
        $game = $platform->games()->firstOrCreate($request->except('platform'));

        return redirect()->route('games.show', compact('game'));
    }

    /**
     * @OA\Get(
     * path="/games/{game:slug}",
     * summary="Games Show",
     * description="View specified game",
     * operationId="gamesShow",
     * tags={"Games"},
     *   @OA\Parameter(
     *      name="game:slug",
     *      in="path",
     *      required=true,
     *      @OA\Schema(
     *           type="string"
     *      )
     *   ),
     * @OA\Response(
     *    response=200,
     *    description="Specified game has been fetched",
     *     @OA\JsonContent(
     *     @OA\Items(
     *      type="object",
     *      ref="#/components/schemas/Game",
     *    )
     *   )
     *  ),
     * @OA\Response(
     *      response=404,
     *      description="Specified game has not been found"
     *   ),
     * )
     * @param \App\Game $game
     * @return \App\Http\Resources\GamesResource
     */
    public function show(Game $game)
    {
        return new GamesResource($game->load('keys.distributor', 'keys.purchases', 'platform'));
    }
}
