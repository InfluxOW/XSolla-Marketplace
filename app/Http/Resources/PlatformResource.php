<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

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
