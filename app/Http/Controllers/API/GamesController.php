<?php

namespace App\Http\Controllers\API;

use App\Distributor;
use App\Game;
use App\Http\Controllers\Controller;
use App\Http\Requests\GamesRequest;
use App\Http\Resources\GamesResource;
use Illuminate\Http\Request;

class GamesController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api', 'seller'])->only('store');
    }

    public function index(Request $request, Distributor $distributor)
    {
        $query = empty($distributor->getAttributes()) ? Game::query() : $distributor->games();
        $games = Game::with('keys.distributor')->paginate(20);

        return GamesResource::collection($games);
    }

    public function store(GamesRequest $request)
    {
        $game = Game::firstOrCreate($request->validated());

        return redirect()->route('games.show', compact('game'));
    }

    public function show(Game $game)
    {
        return new GamesResource($game->load('keys.distributor'));
    }
}
