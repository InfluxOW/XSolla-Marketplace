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
}
