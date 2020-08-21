<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Distributor extends Model
{
    protected $fillable = ['name'];
    public $timestamps = false;

    public function keys()
    {
        return $this->hasMany(Key::class);
    }

    public function games()
    {
        return $this->hasManyThrough(Game::class, Key::class, 'distributor_id', 'id', null, 'game_id');
    }
}
