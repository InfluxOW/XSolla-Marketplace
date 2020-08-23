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
            'link' => $this->when(! $request->is('api/games/*'), route('games.show', ['game' => $this])),
            'keys_count' => $this->getKeysCountGroupedByDistributor(),
        ];
    }

    protected function getKeysCountGroupedByDistributor()
    {
        return $this->whenLoaded('keys')
            ->groupBy(function ($item) {
                return $item->distributor->name;
            })
            ->map(function ($keys) {
                return $keys->count();
            });
    }
}
