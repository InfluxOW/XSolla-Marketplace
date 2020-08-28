<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 *
 * @OA\Schema(
 * @OA\Xml(name="DistributorResource"),
 * @OA\Property(property="name", type="string", readOnly="true", example="Steam"),
 * @OA\Property(property="slug", type="string", readOnly="true", example="steam"),
 * @OA\Property(property="platform", type="string", readOnly="true", example="PC"),
 * @OA\Property(property="total_games", type="integer", readOnly="true", example="5"),
 * @OA\Property(property="available_games", type="integer", readOnly="true", example="2"),
 * @OA\Property(property="link", type="integer", readOnly="true", example="http://localhost:8000/api/games?filter%5Bdistributor%5D=steam"),
 * )
 *
 */
class DistributorResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'name' => $this->name,
            'slug' => $this->slug,
            'platform' => $this->platform->name,
            'total_games' => $this->whenLoaded('games', $this->games->count()),
            'available_games' => $this->games()->availableAtDistributor($this->resource->slug)->count(),
            'link' => route('games.index', ['filter[distributor]' => $this->resource->slug])
        ];
    }
}
