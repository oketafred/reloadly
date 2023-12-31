<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use OTIFSolutions\Laravel\Settings\Models\Setting;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Operator extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $appends = ['select_amounts'];

    protected $casts = [
        'logo_urls' => 'array',
        'fixed_amounts' => 'array',
        'fixed_amounts_descriptions' => 'array',
        'suggested_amounts' => 'array',
        'suggested_amounts_map' => 'array',
        'local_fixed_amounts' => 'array',
        'local_fixed_amounts_descriptions' => 'array',
        'geographical_recharge_plans' => 'array',
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function discount(): HasOne
    {
        return $this->hasOne(Discount::class);
    }

    public function topups(): HasMany
    {
        return $this->hasMany(Topup::class);
    }

    /* public function numbers(){
         return $this->hasMany('App\MobileNumber');
     }*/

    public function promotions(): HasMany
    {
        return $this->hasMany(Promotion::class);
    }

    public function resellers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'reseller_rates', 'operator_id', 'user_id')->withPivot(['international_discount', 'local_discount']);
    }

    public function getFxRateAttribute($fx)
    {
        $user = Auth::user();
        if (isset($user) && ($user['user_role']['name'] === 'RESELLER')) {
            return $fx;
        }

        if (Setting::get('customer_rate')) {
            return $fx * (1 - (Setting::get('customer_rate') / 100));
        }

        return $fx;
    }

    public function getFxForAmount($amount)
    {
        $system = User::admin();
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $system['reloadly_api_url'] . '/operators/fx-rate');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type:application/json',
            'Authorization: Bearer ' . Setting::get('reloadly_api_token'),
        ]);

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            'operatorId' => $this['rid'],
            'currencyCode' => Setting::get('reloadly_currency'),
            'amount' => $amount,
        ]));

        $response = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($response);

        return $response->fxRate ?? -1;
    }

    public function getSelectAmountsAttribute()
    {
        return $this['denomination_type'] === 'RANGE' ? $this['suggested_amounts'] : $this['fixed_amounts'];
    }
}
