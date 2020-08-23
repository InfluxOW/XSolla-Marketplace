<?php

namespace App;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use Sluggable;

    protected $fillable = ['name', 'description', 'price'];
    protected $appends = ['isAvailable'];
    protected $with = ['availableKeys'];

    public function platform()
    {
        return $this->belongsTo(Platform::class);
    }

    public function keys()
    {
        return $this->hasMany(Key::class);
    }

    public function availableKeys()
    {
        return $this->hasMany(Key::class)->available();
    }

    public function keysAtDistributor(Distributor $distributor)
    {
        return $this->keys->where('distributor_id', $distributor->id);
    }

    public function distributors()
    {
        return $this->hasManyThrough(Distributor::class, Key::class, 'owner_id', 'id', 'id', 'distributor_id')
            ->join('games', 'games.id', '=', 'keys.game_id')
            ->distinct('name');
    }

    public function priceIncludingCommission()
    {
        return $this->price * (1 - config('app.marketplace.commission'));
    }

    public function sales()
    {
        return $this->hasManyThrough(Purchase::class, Key::class);
    }

    public function isAvailable()
    {
        return $this->keys->filter->isAvailable()->count() > 0;
    }

    public function getIsAvailableAttribute()
    {
        return $this->isAvailable();
    }

    public function scopeHasDistributorWithAvailableKeys(Builder $query, $distributor): Builder
    {
        return $query->whereHas('distributors', function (Builder $query) use ($distributor) {
            return $query->where('slug', $distributor)->whereHas('availableKeys');
        });
    }

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->whereHas('keys', fn(Builder $query) => $query->whereDoesntHave('purchase'));
    }

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }
}
