<?php

namespace App;

use http\Exception\InvalidArgumentException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Key extends Model
{
    protected $fillable = ['serial_number'];
    protected $appends = ['isAvailable'];
    protected $touches = ['game'];

    /*
     * Relations
     * */

    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function distributor()
    {
        return $this->belongsTo(Distributor::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    /*
     * Helpers
     * */

    public static function createManyByRequest(Request $request)
    {
        if (isset($request->keys, $request->game, $request->distributor) && $request->user()) {
            return collect($request->keys)->map(function ($serial) use ($request) {
                $key = self::make(['serial_number' => $serial]);
                $key->owner()->associate($request->user());
                $key->game()->associate($request->game);
                $key->distributor()->associate($request->distributor);
                $key->save();

                return $key;
            });
        }

        throw new InvalidArgumentException("Your request doesn't have enough arguments. It should contains keys, game, distributor.");
    }

    /*
     * Check if user has reserved a key
     * */

    public function isReservedBy(User $user)
    {
        return $this->purchases->filter->isUnconfirmed()->where('buyer_id', $user->id)->count() > 0;
    }

    /*
     * Check availability
     * */

    public function scopeAvailable(Builder $query)
    {
        return $query->whereDoesntHave('purchases', function (Builder $query) {
            return $query->confirmed();
        });
    }

    public function scopeAvailableAtDistributor(Builder $query, $distributor): Builder
    {
        $distributor = Distributor::whereSlug($distributor)->firstOrFail();

        return $query->available()->where('distributor_id', $distributor->id);
    }

    public function getIsAvailableAttribute()
    {
        return $this->isAvailable();
    }

    public function isAvailable()
    {
        return is_null($this->purchases->whereNotNull('confirmed_at')->first());
    }
}
