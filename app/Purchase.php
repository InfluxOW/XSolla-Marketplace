<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    public $timestamps = false;
    protected $fillable = ['made_at'];

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function key()
    {
        return $this->belongsTo(Key::class);
    }
}
