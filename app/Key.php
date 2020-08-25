<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

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
     * Check if user has reserved a key
     * */

    public function isReservedBy(User $user)
    {
        return $this->purchases->filter->isIncompleted()->where('buyer_id', $user->id)->count() > 0;
    }

    /*
     * Check availability
     * */

    public function scopeAvailable(Builder $query)
    {
        return $query->whereDoesntHave('purchases', function (Builder $query) {
            return $query->completed();
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
        return is_null($this->purchases->whereNotNull('made_at')->first());
    }
}
