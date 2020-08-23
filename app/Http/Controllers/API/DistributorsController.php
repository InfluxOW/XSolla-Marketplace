<?php

namespace App\Http\Controllers\API;

use App\Distributor;
use App\Http\Controllers\Controller;
use App\Http\Resources\DistributorsResource;
use App\Http\Resources\GamesResource;

class DistributorsController extends Controller
{
    public function index()
    {
        $distributors = Distributor::withCount('games')->get();

        return DistributorsResource::collection($distributors);
    }

    public function show(Distributor $distributor)
    {
        $games = $distributor->games()->with('keys.distributor', 'platform')->paginate(20);

        return GamesResource::collection($games);
    }
}
