<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public $timestamps = false;

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function key()
    {
        return $this->belongsTo(Key::class);
    }

    public function purchase()
    {
        return $this->hasOne(Purchase::class);
    }

    public function scopeAvailable(Builder $query)
    {
        return $query->whereDoesntHave('purchase');
    }
}
