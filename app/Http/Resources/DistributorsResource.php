<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DistributorsResource extends JsonResource
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
