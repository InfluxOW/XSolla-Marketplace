<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\PlatformResource;
use App\Platform;

class PlatformsController extends Controller
{
    public function index()
    {
        $platforms = Platform::with('distributors', 'distributors.games', 'distributors.platform', 'games')->get();

        return PlatformResource::collection($platforms);
    }
}
