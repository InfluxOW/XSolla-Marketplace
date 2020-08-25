<?php

namespace App;

use App\Exceptions\NoAvailableKeysException;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

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
