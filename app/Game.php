<?php

namespace App;

use Cviebrock\EloquentSluggable\Sluggable;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use Sluggable;

    protected $fillable = ['name', 'description', 'price'];
    protected $appends = ['isAvailable'];
    protected $with = ['keys'];

    public function keys()
    {
        return $this->hasMany(Key::class);
    }

    public function keysAtDistributor(Distributor $distributor)
    {
        return $this->keys->where('distributor_id', $distributor->id);
    }

    public function distributors()
    {
        return $this->hasManyThrough(Distributor::class, Key::class, null, 'id', 'id', 'distributor_id')
            ->join('games', 'games.id', '=', 'keys.game_id')
            ->distinct('name');
    }

    public function platform()
    {
        return $this->belongsTo(Platform::class);
    }

    public function sales()
    {
        return $this->hasManyThrough(Purchase::class, Key::class);
    }

    public function scopeAvailable(Builder $query)
    {
        return $query->whereHas('keys', fn(Builder $query) => $query->whereDoesntHave('purchase'));
    }

    public function isAvailable()
    {
        return $this->keys->filter->isAvailable()->count() > 0;
    }

    public function getIsAvailableAttribute()
    {
        return $this->isAvailable();
    }

    public function priceIncludingCommission()
    {
        $commission = config('app.marketplace.commission');
        return $this->price * (1 - $commission);
    }

    public function scopePriceLte(Builder $query, $price): Builder
    {
        return $query->where('price', '<=', $price);
    }

    public function scopePriceGte(Builder $query, $price): Builder
    {
        return $query->where('price', '>=', $price);
    }

    public static function index()
    {
        $query = QueryBuilder::for(self::class)
            ->allowedFilters([
                AllowedFilter::exact('platform', 'platform.slug'),
                AllowedFilter::exact('distributor', 'distributors.slug'),
                AllowedFilter::scope('price_lte'),
                AllowedFilter::scope('costs_gte'),
            ])
            ->allowedSorts([
                AllowedSort::field('platform', 'platform_id'),
                AllowedSort::field('price'),
                AllowedSort::field('name'),
            ])
            ->latest();

        return $query->with('keys.distributor', 'platform')->paginate(20)->appends(request()->query());
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
