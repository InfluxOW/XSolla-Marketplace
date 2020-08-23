<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GamesResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'name' => $this->name,
            'description' => $this->when(! is_null($this->description), $this->description),
            'platform' => $this->whenLoaded('platform', $this->platform->name),
            'price' => $this->price,
            'link' => $this->when(! $request->is('api/games/*'), route('games.show', ['game' => $this->resource])),
            'keys_count' => $this->getKeysCountGroupedByDistributor(),
        ];
    }

    protected function getKeysCountGroupedByDistributor()
    {
        return $this->whenLoaded('availableKeys')
            ->groupBy(function ($item) {
                return $item->distributor->name;
            })
            ->map->count();
    }
}
