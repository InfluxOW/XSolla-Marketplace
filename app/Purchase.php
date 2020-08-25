<?php

namespace App;

use App\Events\PurchaseConfirmed;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    public $timestamps = false;
    protected $fillable = ['made_at', 'payment_session_token'];
    protected $casts = [
        'made_at' => 'datetime',
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
        $this->update(['made_at' => now()]);
        
        PurchaseConfirmed::dispatch($this);
    }

    /*
     * Check completeness
     * */

    public function scopeCompleted(Builder $query)
    {
        return $query->whereNotNull('made_at');
    }

    public function scopeIncompleted(Builder $query)
    {
        return $query->whereNull('made_at');
    }

    public function isCompleted()
    {
        return isset($this->made_at);
    }

    public function isIncompleted()
    {
        return is_null($this->made_at);
    }
}
