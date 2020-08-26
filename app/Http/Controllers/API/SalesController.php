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

    public function store(SalesRequest $request, Game $game)
    {
        Key::createManyByRequest($request);

        return response("You have successfully put up for sale keys for the game {$game->name} at distributor {$request->distributor->name}!", 201);
    }
}
