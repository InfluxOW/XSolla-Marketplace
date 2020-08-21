<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    protected $fillable = ['name', 'description', 'price'];

    public function keys()
    {
        return $this->hasMany(Key::class);
    }

    public function sales()
    {
        return $this->hasManyThrough(Purchase::class, Key::class);
    }
}
