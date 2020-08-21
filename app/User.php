<?php

namespace App;

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
        'name', 'username', 'email', 'password', 'role'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
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

    public function sales()
    {
        return $this->hasManyThrough(Purchase::class, Key::class,'seller_id');
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

    public function purchases()
    {
        return $this->hasMany(Purchase::class, 'buyer_id');
    }
}
