<?php

namespace App;

use App\Events\PaymentConfirmed;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Payment extends Model
{
    public $timestamps = false;
    protected $fillable = ['confirmed_at', 'token'];
    protected $casts = [
        'confirmed_at' => 'datetime',
    ];
    protected $hidden = ['token'];

    protected static function booted()
    {
        static::updated(function () {
            Cache::delete('distributors');
        });
    }

    /*
     * Relations
     * */

    public function key()
    {
        return $this->belongsTo(Key::class);
    }

    public function payer()
    {
        return $this->belongsTo(User::class, 'payer_id');
    }

    /*
     * Helpers
     * */

    public function confirm()
    {
        $this->update(['confirmed_at' => now()]);

        PaymentConfirmed::dispatch($this);
    }

    /*
     * Check completeness
     * */

    public function scopeConfirmed(Builder $query)
    {
        return $query->whereNotNull('confirmed_at');
    }

    public function scopeUnconfirmed(Builder $query)
    {
        return $query->whereNull('confirmed_at');
    }

    public function isConfirmed()
    {
        return isset($this->confirmed_at);
    }

    public function isUnconfirmed()
    {
        return is_null($this->confirmed_at);
    }
}
