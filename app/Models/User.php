<?php

namespace App\Models;

use App\Traits\ReloadlySystem;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use OTIFSolutions\ACLMenu\Traits\ACLUserTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory, ACLUserTrait, Notifiable, HasApiTokens;
    use ReloadlySystem;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'stripe_response' => 'array',
    ];

    public static function admin()
    {
        return self::first();
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function stripe_payment_methods()
    {
        return $this->hasMany(StripePaymentMethod::class);
    }

    public function default_stripe_payment_method()
    {
        return $this->belongsTo(StripePaymentMethod::class, 'stripe_payment_method_id', 'id');
    }

    public function account_transactions()
    {
        return $this->hasMany(AccountTransaction::class)->orderBy('id', 'DESC');
    }

    public function getBalanceValueAttribute()
    {
        $balanceItem = $this->account_transactions()->orderBy('id', 'DESC')->first();

        return $balanceItem ? $balanceItem['ending_balance'] : 0;
    }

    public function topups()
    {
        return $this->hasMany(Topup::class);
    }

    public function files()
    {
        return $this->hasMany(File::class);
    }

    public function operators()
    {
        return $this->belongsToMany(Operator::class, 'reseller_rates', 'user_id', 'operator_id')->withPivot(['international_discount', 'local_discount']);
    }

    public function api_operators()
    {
        return $this->belongsToMany(Operator::class, 'reseller_rates', 'user_id', 'operator_id')->as('rates')->withPivot(['international_discount', 'local_discount']);
    }

    public function gift_cards()
    {
        return $this->belongsToMany(GiftCardProduct::class, 'reseller_gift_card_rates', 'user_id', 'gift_card_product_id')->withPivot(['discount']);
    }

    public function ips()
    {
        return $this->hasMany(IpAddress::class, 'user_id');
    }
}
