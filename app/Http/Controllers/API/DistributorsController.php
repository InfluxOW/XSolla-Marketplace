<?php

namespace App\Http\Controllers\API;

use App\Distributor;
use App\Http\Controllers\Controller;
use App\Http\Resources\DistributorResource;

class DistributorsController extends Controller
{
    public function index()
    {
        $distributors = Distributor::with('games', 'platform')->get();

        return DistributorResource::collection($distributors);
    }
}
