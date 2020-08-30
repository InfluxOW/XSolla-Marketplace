<?php

namespace App;

use App\Events\PaymentConfirmed;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Payment extends Model
{
    public $timestamps = false;
    protected $fillable = ['confirmed_at', 'token', 'reserved_until'];
    protected $casts = [
        'confirmed_at' => 'datetime',
        'reserved_until' => 'datetime',
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
     * Check reserve
     * */

    public function scopeReserved(Builder $query)
    {
        return $query->where('reserved_until', '>', now());
    }

    public function isReserved()
    {
        return $this->reserved_until > now();
    }

    public function scopeOutdated(Builder $query)
    {
        return $query->where('reserved_until', '<=', now());
    }

    public function isOutdated()
    {
        return $this->reserved_until <= now();
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
