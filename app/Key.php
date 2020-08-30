<?php

namespace App;

use App\Http\Requests\SalesRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Key extends Model
{
    protected $fillable = ['serial_number'];

    protected static function booted()
    {
        static::created(function () {
            Cache::delete('distributors');
        });
    }

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

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /*
     * Helpers
     * */

    public static function createManyByRequest(SalesRequest $request)
    {
        return collect($request->keys)->map(function ($serial) use ($request) {
            $key = self::make(['serial_number' => $serial]);
            $key->owner()->associate($request->user());
            $key->game()->associate($request->game);
            $key->distributor()->associate($request->distributor);
            $key->save();

            return $key;
        });
    }

    /*
     * Check if user has reserved a key
     * */

    public function isReservedBy(User $user)
    {
        return $this->payments->where('payer_id', $user->id)->filter->isUnconfirmed()->count() > 0;
    }

    /*
     * Check availability
     * */

    public function scopeAvailable(Builder $query)
    {
        return $query->whereDoesntHave('payments', function (Builder $query) {
            return $query->confirmed();
        });
    }

    public function scopeAvailableAtDistributor(Builder $query, $distributor): Builder
    {
        $distributor = (Cache::get('distributors') ?? Distributor::all())->firstWhere('slug', $distributor);

        if (is_null($distributor)) {
            throw new InvalidArgumentException('Distributor with specified slug has not been found');
        }

        return $query->available()->where('distributor_id', $distributor->id);
    }

    public function isAvailable()
    {
        return $this->payments->whereNotNull('confirmed_at')->isEmpty();
    }
}
