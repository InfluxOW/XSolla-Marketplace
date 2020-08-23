<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PlatformsResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'name' => $this->name,
            'link' => route('games.index', ['filters[platform]' => $this->resource->slug]),
            'distributors' => $this->whenLoaded('distributors', DistributorsResource::collection($this->distributors)),
        ];
    }
}
