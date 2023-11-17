<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OTIFSolutions\Laravel\Settings\Models\Setting;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FileEntry extends Model
{
    protected $guarded = ['id'];

    protected $hidden = [
        'country_id',
        'created_at',
        'updated_at',
        'file_id',
    ];

    protected $with = ['country', 'operator'];

    protected $appends = ['operators'];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function operator(): BelongsTo
    {
        return $this->belongsTo(Operator::class);
    }

    public function getOperatorsAttribute()
    {
        return Operator::where('country_id', $this['country_id'])->get();
    }

    public function getEstimatesAttribute(): array
    {
        return [
            'amount' => round($this['is_local'] ? ($this['amount'] / $this['operator']['fx_rate']) : $this['amount'], 2) . ' ' . @Setting::get('reloadly_currency'),
            'topup' => round($this['is_local'] ? $this['amount'] : ($this['amount'] * $this['operator']['fx_rate']), 2) . ' ' . $this['operator']['destination_currency_code'],
        ];
    }
}
