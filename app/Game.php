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

    /*
     * Check availability
     * */

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->whereHas('keys', fn(Builder $query) => $query->whereDoesntHave('purchase'));
    }

    public function scopeAvailableAtDistributor(Builder $query, $distributor): Builder
    {
        $distributor = Distributor::whereSlug($distributor)->firstOrFail();

        return $query->whereHas('keys', fn(Builder $query) => $query->where('distributor_id', $distributor->id)->whereDoesntHave('purchase'));
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
