<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\PlatformResource;
use App\Platform;
use Illuminate\Support\Facades\Cache;

class PlatformsController extends Controller
{
    /**
     * @OA\Get(
     * path="/platforms",
     * summary="Platforms Index",
     * description="View all platforms",
     * operationId="platformsIndex",
     * tags={"Platforms"},
     * @OA\Response(
     *    response=200,
     *    description="Platforms were fetched",
     *     @OA\JsonContent(
     *     @OA\Property(
     *      property="platforms",
     *      type="object",
     *      collectionFormat="multi",
     *       @OA\Property(
     *         property="0",
     *         type="array",
     *         collectionFormat="multi",
     *         @OA\Items(
     *           type="object",
     *           ref="#/components/schemas/PlatformResource",
     *        )
     *      ),
     *    )
     *   )
     *  ),
     * )
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        $platforms = Cache::rememberForever('platforms', function () {
            return Platform::with('distributors', 'distributors.games', 'distributors.platform', 'games')->get();
        });

        return PlatformResource::collection($platforms);
    }
}
