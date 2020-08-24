<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\PlatformsResource;
use App\Platform;

class PlatformsController extends Controller
{
    public function index()
    {
        $platforms = Platform::with('distributors', 'distributors.games', 'games')->get();

        return PlatformsResource::collection($platforms);
    }
}
