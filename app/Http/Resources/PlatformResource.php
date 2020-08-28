<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 *
 * @OA\Schema(
 * @OA\Xml(name="PlatformResource"),
 * @OA\Property(property="name", type="string", readOnly="true", example="PC"),
 * @OA\Property(property="slug", type="string", readOnly="true", example="pc"),
 * @OA\Property(property="total_games", type="integer", readOnly="true", example="5"),
 * @OA\Property(property="link", type="integer", readOnly="true", example="http://localhost:8000/api/games?filter%5Bplatform%5D=pc"),
 * @OA\Property(property="distributors", type="array", readOnly="true", @OA\Items(type="object",ref="#/components/schemas/DistributorResource")),
 * )
 *
 */
class PlatformResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'name' => $this->name,
            'slug' => $this->slug,
            'total_games' =>  $this->whenLoaded('games', $this->games->count()),
            'link' => route('games.index', ['filter[platform]' => $this->resource->slug]),
            'distributors' => $this->whenLoaded('distributors', DistributorResource::collection($this->distributors)),
        ];
    }
}
