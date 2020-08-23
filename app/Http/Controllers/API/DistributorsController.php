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
        $distributors = Distributor::with('games')->get();

        return DistributorsResource::collection($distributors);
    }
}
