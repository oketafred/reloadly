<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GiftCardTransaction extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'product' => 'array',
        'response' => 'array',
    ];

    protected $appends = ['message'];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function sender_currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'sender_currency_id');
    }

    public function recipient_currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'recipient_currency_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(GiftCardProduct::class, 'product_id');
    }

    public function sendTransaction()
    {
        $response = User::admin()->orderReloadlyGiftProducts($this['product']['rid'], $this['product']['country']['isoName'], 1, $this['recipient_amount'], $this['reference'], $this['user']['name'], $this['email']);

        if ((isset($response['status'])) && ($response['status'] === 'SUCCESSFUL')) {
            $this['transaction_id'] = $response['transactionId'];
            $this['status'] = 'SUCCESS';
        } else {
            $this['status'] = 'FAIL';
        }
        $this['response'] = $response;
        $this->save();
    }

    public function getMessageAttribute()
    {
        switch ($this['status']) {
            case 'PENDING':
                return 'Transaction is paid. But its pending transaction. Please wait a few minuites for the status to update.';
            case 'SUCCESS':
                return 'Transaction completed successfully.';
            case 'FAIL':
                return $this['response']['message'] ?? 'Transaction Failed. No response';
            case 'PENDING_PAYMENT':
                return 'Transaction is pending payment';
            case 'REFUNDED':
                return 'Gift Card Transaction has been refunded. It failed due to Error : ' . ($this['response']['message'] ?? 'Unknown');
            default:
                return 'Error : Unknown Status found.';
        }
    }
}
