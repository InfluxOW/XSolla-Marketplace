<?php

namespace App\Http\Controllers\API;

use App\Http\Resources\PlatformsResource;
use App\Platform;

class PlatformsController
{
    public function __invoke()
    {
        $platforms = Platform::with('distributors', 'distributors.games', 'games')->get();

        return PlatformsResource::collection($platforms);
    }
}
