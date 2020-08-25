<?php

namespace App\Http\Controllers\API;

use App\Game;
use App\Http\Controllers\Controller;
use App\Http\Requests\GamesRequest;
use App\Http\Resources\GamesResource;
use App\Platform;
use App\Repositories\GameRepository;
use Illuminate\Http\Request;

class GamesController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api', 'seller'])->only('store');
    }

    public function index()
    {
        $games = GameRepository::getGamesForIndexPage();

        return GamesResource::collection($games);
    }

    public function store(GamesRequest $request)
    {
        $platform = Platform::whereSlug($request->platform)->first();
        $game = $platform->games()->firstOrCreate($request->validated());

        return redirect()->route('games.show', compact('game'));
    }

    public function show(Game $game)
    {
        return new GamesResource($game->load('keys.distributor', 'keys.purchases', 'platform'));
    }
}
