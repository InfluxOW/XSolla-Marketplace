<?php

namespace App;

use App\Exceptions\NoAvailableKeysException;
use App\Http\Requests\GamesRequest;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class Game extends Model
{
    use Sluggable;

    protected $fillable = ['name', 'description', 'price'];
    protected $with = ['keys'];
    protected $casts = [
        'price' => 'integer'
    ];

    protected static function booted()
    {
        static::created(function () {
            Cache::delete('platforms');
        });
    }

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

    /*
     * Helpers
     * */

    public static function createByRequest(GamesRequest $request)
    {
        $platform = (Cache::get('platforms') ?? Platform::all())->firstWhere('name', $request->platform);

        return $platform->games()->firstOrCreate($request->except('platform'));
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
