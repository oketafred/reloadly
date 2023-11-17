<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use OTIFSolutions\Laravel\Settings\Models\Setting;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Discount extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function operator()
    {
        return $this->belongsTo(Operator::class);
    }

    public function getInternationalPercentageResellerAttribute()
    {
        $user = Auth::user();
        if (isset($user) && ($user['user_role']['name'] == 'RESELLER') && (Setting::get('reseller_discount'))) {
            return $this['international_percentage'] * (Setting::get('reseller_discount') / 100);
        }

        return 0;
    }

    public function getLocalPercentageResellerAttribute()
    {
        $user = Auth::user();
        if (isset($user) && ($user['user_role']['name'] == 'RESELLER') && (Setting::get('reseller_discount'))) {
            return $this['local_percentage'] * (Setting::get('reseller_discount') / 100);
        }

        return 0;
    }
}
