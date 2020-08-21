<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Key extends Model
{
    protected $fillable = ['serial_number'];

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

    public function scopeAvailable(Builder $query)
    {
        return $query->whereDoesntHave('purchase');
    }

    public function isAvailable()
    {
        return Purchase::where('key_id', $this->id)->doesntExist();
    }
}
