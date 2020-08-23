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
        return $this->hasManyThrough(Distributor::class, Key::class, 'game_id', 'id', 'id', 'distributor_id')
            ->join('games', 'games.id', '=', 'keys.game_id')
            ->distinct('name');
    }

    public function sales()
    {
        return $this->hasManyThrough(Purchase::class, Key::class);
    }

    /*
     * Helping relations
     * */

    public function availableKeys()
    {
        return $this->keys()->available();
    }

    public function distributorsWithAvailableKeys()
    {
        return $this->distributors()->whereHas('keys', function (Builder $query) {
            return $query->available()->where('game_id', $this->id);
        });
    }

    public function keysAtDistributor(Distributor $distributor)
    {
        return $this->keys()->where('distributor_id', $distributor->id);
    }

    /*
     * Getters
     * */

    public function getPriceIncludingCommission()
    {
        return $this->price * (1 - config('app.marketplace.commission'));
    }

    /*
     * Check availability
     * */

    public function isAvailable()
    {
        return $this->keys->filter->isAvailable()->count() > 0;
    }

    public function getIsAvailableAttribute()
    {
        return $this->isAvailable();
    }

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->whereHas('keys', fn(Builder $query) => $query->whereDoesntHave('purchase'));
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
