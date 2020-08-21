<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    protected $fillable = ['name', 'description', 'price'];
    protected $appends = ['isAvailable'];
    protected $with = ['keys'];

    public function keys()
    {
        return $this->hasMany(Key::class);
    }

    public function sales()
    {
        return $this->hasManyThrough(Purchase::class, Key::class);
    }

    public function scopeAvailable(Builder $query)
    {
        return $query->whereHas('keys', fn(Builder $query) => $query->whereDoesntHave('purchase'));
    }

    public function getIsAvailableAttribute()
    {
        return $this->isAvailable();
    }

    public function isAvailable()
    {
        return $this->keys->filter->isAvailable()->count() > 0;
    }

    public function priceIncludingCommission()
    {
        $commission = config('app.marketplace.commission');
        return $this->price * (1 - $commission);
    }
}
