<?php

namespace App;

use App\Events\PaymentConfirmed;
use App\Helpers\Billing;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable;
    use HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'username', 'email', 'password', 'role',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'email_verified_at'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /*
     * For sellers
     * */

    public function isSeller()
    {
        return $this->role === 'seller';
    }

    public function scopeSeller(Builder $query)
    {
        $query->where('role', 'seller');
    }

    public function keys()
    {
        return $this->hasMany(Key::class, 'owner_id');
    }

    public function sales()
    {
        return $this->hasManyThrough(Payment::class, Key::class, 'owner_id');
    }

    /*
     * For buyers
     * */

    public function isBuyer()
    {
        return $this->role === 'buyer';
    }

    public function scopeBuyer(Builder $query)
    {
        $query->where('role', 'buyer');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'payer_id');
    }

    public function reserve(Key $key)
    {
        return tap($this->payments()->make(), function ($payment) use ($key) {
            $payment->key()->associate($key);
            $payment->token = Billing::generatePaymentSessionToken($key, $this->email, $key->game->price);
            $payment->reserved_until = now()->addHour();
            $payment->save();
        });
    }
}
