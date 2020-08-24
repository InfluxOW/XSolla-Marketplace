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

    public function purchase()
    {
        return $this->hasOne(Purchase::class);
    }

    /*
     * Check availability
     * */

    public function scopeAvailable(Builder $query)
    {
        return $query->whereDoesntHave('purchase');
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
        return is_null($this->purchase);
    }
}
