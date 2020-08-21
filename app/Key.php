<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Key extends Model
{
    protected $fillable = ['serial_number'];
    public $timestamps = false;

    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function distributor()
    {
        return $this->belongsTo(Distributor::class);
    }

    public function product()
    {
        return $this->hasOne(Product::class);
    }

    public function scopeAvailable(Builder $query)
    {
        return $query->whereDoesntHave('product');
    }

    public function isAvailable()
    {
        return Purchase::where('key_id', $this->id)->exists();
    }
}
