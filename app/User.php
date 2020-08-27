<?php

namespace App;

use App\Events\PurchaseConfirmed;
use App\Helpers\Billing;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

/**
 *
 * @OA\Schema(
 * required={"password"},
 * @OA\Xml(name="User"),
 * @OA\Property(property="id", type="integer", readOnly="true", example="1"),
 * @OA\Property(property="name", type="string", readOnly="true", example="John Doe"),
 * @OA\Property(property="username", type="string", example="john_doe"),
 * @OA\Property(property="balance", type="integer", readOnly="true", example="0"),
 * @OA\Property(property="email", type="string", readOnly="true", format="email", example="user@gmail.com"),
 * @OA\Property(property="role", type="string", readOnly="true", enum={"seller", "buyer"}),
 * @OA\Property(property="email_verified_at", type="string", readOnly="true", format="date-time", example="2019-02-25 12:59:20"),
 * @OA\Property(property="created_at", type="string", format="date-time", readOnly="true", example="2019-02-25 12:59:20"),
 * @OA\Property(property="updated_at", type="string", format="date-time", readOnly="true", example="2019-02-25 12:59:20"),
 * )
 *
 */
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
        'name', 'username', 'email', 'password', 'role', 'email_verified_at', 'remember_token'
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

    public function keys()
    {
        return $this->hasMany(Key::class, 'owner_id');
    }

    public function sales()
    {
        return $this->hasManyThrough(Purchase::class, Key::class, 'owner_id');
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

    public function reserve(Key $key)
    {
        return tap($this->purchases()->make(), function ($purchase) use ($key) {
            $purchase->key()->associate($key);
            $purchase->payment_session_token = Billing::generatePaymentToken($key, $this->email, $key->game->price);
            $purchase->save();
        });
    }
}
