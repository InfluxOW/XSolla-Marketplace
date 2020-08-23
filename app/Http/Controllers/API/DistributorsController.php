<?php

namespace App\Http\Controllers\API;

use App\Distributor;
use App\Http\Resources\DistributorsResource;

class DistributorsController
{
    public function __invoke()
    {
        $distributors = Distributor::with('games')->get();

        return DistributorsResource::collection($distributors);
    }
}
