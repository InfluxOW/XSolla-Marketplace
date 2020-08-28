<?php

namespace App\Http\Controllers\API;

use App\Game;
use App\Http\Controllers\Controller;
use App\Http\Requests\GamesRequest;
use App\Http\Resources\GameResource;
use App\Platform;
use App\Repositories\GameRepository;

class GamesController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api', 'seller'])->only('store');
        $this->middleware(['auth:api'])->only('index');
    }

    /**
     * @OA\Get(
     * path="/games",
     * summary="Games Index",
     * description="View all games",
     * operationId="gamesIndex",
     * tags={"Games"},
     *  @OA\Parameter(
     *    name="filter[platform]",
     *    in="query",
     *    description="Filter games by platform:slug",
     *    required=false,
     *    @OA\Schema(
     *         type="string"
     *    )
     *  ),
     *  @OA\Parameter(
     *    name="filter[distributor]",
     *    in="query",
     *    description="Filter games by distributor:slug",
     *    required=false,
     *    @OA\Schema(
     *         type="string"
     *    )
     *  ),
     *  @OA\Parameter(
     *    name="filter[available]",
     *    in="query",
     *    description="Leave only games that are available for purchase",
     *    required=false,
     *    @OA\Schema(
     *         type="boolean"
     *    )
     *  ),
     *  @OA\Parameter(
     *    name="filter[available_at]",
     *    in="query",
     *    description="Leave only games that are available for purchase at the specified distributor:slug",
     *    required=false,
     *    @OA\Schema(
     *         type="string"
     *    )
     *  ),
     *  @OA\Parameter(
     *    name="filter[price_lte]",
     *    in="query",
     *    description="Leave only games that costs less or equals to the specified price",
     *    required=false,
     *    @OA\Schema(
     *         type="integer"
     *    )
     *  ),
     *  @OA\Parameter(
     *    name="filter[price_gte]",
     *    in="query",
     *    description="Leave only games that costs more or equals to the specified price",
     *    required=false,
     *    @OA\Schema(
     *         type="integer"
     *    )
     *  ),
     *  @OA\Parameter(
     *    name="sort",
     *    in="query",
     *    description="Sort games by one of the available params: price, name or platform. Default sort direction is ASC. To apply DESC sort add '-' symbol before the param name.",
     *    required=false,
     *    @OA\Schema(
     *         type="string"
     *    )
     *  ),
     *  @OA\Parameter(
     *    name="page",
     *    in="query",
     *    description="Results page",
     *    required=false,
     *    @OA\Schema(
     *         type="integer"
     *    )
     *  ),
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
     *           ref="#/components/schemas/GameResource",
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

        return GameResource::collection($games);
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
     *       @OA\Items(type="object", ref="#/components/schemas/GameResource"),
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
     *                 example={"Name attribute is required."},
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
     *      ref="#/components/schemas/GameResource",
     *    )
     *   )
     *  ),
     * @OA\Response(
     *      response=404,
     *      description="Specified game has not been found"
     *   ),
     * )
     * @param \App\Game $game
     * @return \App\Http\Resources\GameResource
     */
    public function show(Game $game)
    {
        return new GameResource($game->load('keys.distributor', 'keys.purchases', 'platform'));
    }
}
