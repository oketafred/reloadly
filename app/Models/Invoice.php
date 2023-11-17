<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Invoice extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'payment_intent_response' => 'array',
        'paypal_response' => 'array',
    ];

    public function topups(): HasMany
    {
        return $this->hasMany(Topup::class);
    }

    public function topup(): HasOne
    {
        return $this->hasOne(Topup::class, 'invoice_id');
    }

    public function gift_card(): HasOne
    {
        return $this->hasOne(GiftCardTransaction::class, 'invoice_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
