<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 *
 * @OA\Schema(
 * @OA\Xml(name="GameResource"),
 * @OA\Property(property="name", type="string", readOnly="true", example="The Witcher 3: Wild Hunt"),
 * @OA\Property(property="slug", type="string", readOnly="true", example="the-witcher-3-wild-hunt"),
 * @OA\Property(property="description", type="string", readOnly="true", example="The Witcher 3: Wild Hunt is a 2015 action role-playing game developed and published by Polish developer CD Projekt Red and is based on The Witcher series of fantasy novels by Andrzej Sapkowski. It is the sequel to the 2011 game The Witcher 2: Assassins of Kings and the third main installment in the The Witcher's video game series, played in an open world with a third-person perspective."),
 * @OA\Property(property="platform", type="string", readOnly="true", example="PC"),
 * @OA\Property(property="price", type="integer", readOnly="true", example="50"),
 * @OA\Property(property="link", type="string", readOnly="true", example="http://localhost:8000/api/games/the-witcher-3-wild-hunt"),
 * @OA\Property(property="available_keys", type="string[]", readOnly="true", example={"Uplay" = 3, "Steam" = 2}),
 * )
 *
 */
class GameResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->when(! is_null($this->description), $this->description),
            'platform' => $this->whenLoaded('platform', $this->platform->name),
            'price' => $this->price,
            'link' => $this->when(! $request->is('api/games/*'), route('games.show', ['game' => $this->resource])),
            'available_keys' => $this->getAvailableKeysCount(),
        ];
    }

    protected function getAvailableKeysCount()
    {
        return  $this->whenLoaded(
            'keys',
            $this->keys
                ->groupBy(function ($key) {
                    return $key->distributor->name;
                })
                ->map(function ($keys) {
                    return $keys->filter->isAvailable()->count();
                })
        );
    }
}
