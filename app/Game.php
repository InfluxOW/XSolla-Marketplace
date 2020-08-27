<?php

namespace App;

use App\Exceptions\NoAvailableKeysException;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 *
 * @OA\Schema(
 * @OA\Xml(name="Game"),
 * @OA\Property(property="name", type="string", readOnly="true", example="The Witcher 3: Wild Hunt"),
 * @OA\Property(property="description", type="string", readOnly="true", example="The Witcher 3: Wild Hunt is a 2015 action role-playing game developed and published by Polish developer CD Projekt Red and is based on The Witcher series of fantasy novels by Andrzej Sapkowski. It is the sequel to the 2011 game The Witcher 2: Assassins of Kings and the third main installment in the The Witcher's video game series, played in an open world with a third-person perspective."),
 * @OA\Property(property="platform", type="string", readOnly="true", example="PC"),
 * @OA\Property(property="price", type="integer", readOnly="true", example="50"),
 * @OA\Property(property="link", type="string", readOnly="true", example="http://localhost:8000/api/games/the-witcher-3-wild-hunt"),
 * @OA\Property(property="available_keys", type="string[]", readOnly="true", example={"Uplay" = 3, "Steam" = 2}),
 * )
 *
 */
class Game extends Model
{
    use Sluggable;

    protected $fillable = ['name', 'description', 'price'];
    protected $appends = ['isAvailable'];
    protected $with = ['keys'];
    protected $casts = [
        'price' => 'integer'
    ];

    /*
     * Relations
     * */

    public function platform()
    {
        return $this->belongsTo(Platform::class);
    }

    public function keys()
    {
        return $this->hasMany(Key::class);
    }

    public function distributors()
    {
        return $this->hasManyThrough(Distributor::class, Key::class, 'game_id', 'id', null, 'distributor_id')->distinct('name');
    }

    public function sales()
    {
        return $this->hasManyThrough(Purchase::class, Key::class);
    }

    /*
     * Getters
     * */

    public function getPriceIncludingCommission()
    {
        return $this->price * (1 - config('app.marketplace.commission'));
    }

    public function getFirstAvailableKeyAtDistributor($distributor)
    {
        $keys =  $this->keys()->availableAtDistributor($distributor);

        if ($keys->exists()) {
            return $keys->first();
        }

        throw new NoAvailableKeysException('Selected game has no available keys at the specified distributor.');
    }

    /*
     * Check availability
     * */

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->whereHas('keys', fn(Builder $query) => $query->available());
    }

    public function scopeAvailableAtDistributor(Builder $query, $distributor): Builder
    {
        return $query->whereHas('keys', fn(Builder $query) => $query->availableAtDistributor($distributor));
    }

    public function isAvailable()
    {
        return $this->keys->filter->isAvailable()->count() > 0;
    }

    public function getIsAvailableAttribute()
    {
        return $this->isAvailable();
    }

    /*
     * Other
     * */

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }
}
