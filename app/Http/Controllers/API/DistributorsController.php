<?php

namespace App\Http\Controllers\API;

use App\Distributor;
use App\Http\Controllers\Controller;
use App\Http\Resources\DistributorResource;
use Illuminate\Support\Facades\Cache;

class DistributorsController extends Controller
{
    /**
     * @OA\Get(
     * path="/distributors",
     * summary="Distributors Index",
     * description="View all distributors",
     * operationId="distributorsIndex",
     * tags={"Distributors"},
     * @OA\Response(
     *    response=200,
     *    description="Distributors were fetched",
     *     @OA\JsonContent(
     *     @OA\Property(
     *      property="distributors",
     *      type="object",
     *      collectionFormat="multi",
     *       @OA\Property(
     *         property="0",
     *         type="array",
     *         collectionFormat="multi",
     *         @OA\Items(
     *           type="object",
     *           ref="#/components/schemas/DistributorResource",
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
        $distributors = Cache::rememberForever('distributors', function () {
            return Distributor::with('games', 'platform')->get();
        });

        return DistributorResource::collection($distributors);
    }
}
