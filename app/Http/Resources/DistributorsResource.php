<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DistributorsResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'name' => $this->name,
            'games_count' => $this->games_count,
            'link' => route('games.index', ['filter[distributor]' => $this->resource->slug])
        ];
    }
}
