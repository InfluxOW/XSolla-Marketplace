<?php

namespace App;

use App\Events\PurchaseConfirmed;
use App\Jobs\NotifySellerAboutSoldKey;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    public $timestamps = false;
    protected $fillable = ['confirmed_at', 'payment_session_token'];
    protected $casts = [
        'confirmed_at' => 'datetime',
    ];
    protected $hidden = ['payment_session_token'];

    /*
     * Relations
     * */

    public function key()
    {
        return $this->belongsTo(Key::class);
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function seller()
    {
        return $this->key->owner();
    }

    /*
     * Helpers
     * */

    public function confirm()
    {
        $this->update(['confirmed_at' => now()]);

        PurchaseConfirmed::dispatch($this);
        NotifySellerAboutSoldKey::dispatch($this);
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
