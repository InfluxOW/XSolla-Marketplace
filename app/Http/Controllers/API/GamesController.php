<?php

namespace App\Http\Controllers\API;

use App\Game;
use App\Http\Controllers\Controller;
use App\Http\Resources\GamesResource;
use Illuminate\Http\Request;

class GamesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api')->only('store', 'update', 'destroy');
    }

    public function index(Request $request)
    {
        $games = Game::available()->with('keys.distributor')->paginate(20);

        return GamesResource::collection($games);
    }

    public function store(Request $request)
    {
        //
    }

    public function show(Game $game)
    {
        return new GamesResource($game->load('keys.distributor'));
    }

    public function update(Request $request, Game $game)
    {
        //
    }

    public function destroy(Game $game)
    {
        //
    }
}
