<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DistributorsResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'name' => $this->name,
            'total_games' => $this->whenLoaded('games', $this->games->count()),
            'link' => route('games.index', ['filter[distributor]' => $this->resource->slug])
        ];
    }
}
