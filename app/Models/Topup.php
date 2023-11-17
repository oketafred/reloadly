<?php

namespace App\Models;

use OTIFSolutions\CurlHandler\Curl;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use OTIFSolutions\Laravel\Settings\Models\Setting;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Topup extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $casts = [
        'response' => 'array',
        'pin' => 'array',
    ];
    protected $appends = ['message'];

    public function operator(): BelongsTo
    {
        return $this->belongsTo(Operator::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function file_entry(): BelongsTo
    {
        return $this->belongsTo(FileEntry::class);
    }

    public function getMessageAttribute()
    {
        switch ($this['status']) {
            case 'PENDING':
                return 'Transaction is paid. But its pending topup. Please wait a few minuites for the status to update.';
            case 'SUCCESS':
                return 'Transaction completed successfully.';
            case 'FAIL':
                return $this['response']['message'] ?? 'Transaction Failed. No response';
            case 'PENDING_PAYMENT':
                return 'Transaction is pending payment';
            case 'REFUNDED':
                return 'Topup has been refunded. It failed due to Error : ' . ($this['response']['message'] ?? 'Unknown');
            default:
                return 'Error : Unknown Status found.';
        }
    }

    public function sendTopup($sendResponse = false)
    {
        if (isset($this['operator']) && isset($this['operator']['country'])) {
            $system = User::admin();

            if ($this['status'] === 'PENDING') {
                $response = Curl::Make()->POST->url($system['reloadly_api_url'] . '/topups')->header([
                    'Content-Type:application/json',
                    'Authorization: Bearer ' . Setting::get('reloadly_api_token'),
                ])->body([
                    'recipientPhone' => [
                        'countryCode' => $this['operator']['country']['iso'],
                        'number' => $this['number'],
                    ],
                    'operatorId' => $this['operator']['rid'],
                    'amount' => $this['is_local'] ? $this['topup'] : $this['topup'] / $this['operator']['fx_rate'],
                    'useLocalAmount' => $this['is_local'] ? 'true' : 'false',
                ])->execute();
            } elseif (isset($this['response']['transactionId'])) {
                $response = Curl::Make()->GET->url($system['reloadly_api_url'] . '/topups/reports/transactions/' . $this['response']['transactionId'])->header([
                    'Content-Type:application/json',
                    'Authorization: Bearer ' . Setting::get('reloadly_api_token'),
                ])->execute();
            } else {
                return null;
            }

            Log::create([
                'task' => 'SEND_TOPUP',
                'params' => 'TOPUP_ID:' . $this['id'] . ' PHONE:' . $this['number'] . ' TOPUP:' . $this['topup'],
                'response' => json_encode($response),
            ]);
            $this['response'] = $response;
            if (isset($this['response']['transactionId']) && $this['response']['transactionId'] != null && $this['response']['transactionId'] != '') {
                if (isset($this['response']['status'])) {
                    switch ($this['response']['status']) {
                        case 'SUCCESSFUL':
                            $this['status'] = 'SUCCESS';
                            if (isset($this['response']['pinDetail'])) {
                                $this['pin'] = $this['response']['pinDetail'];
                            }
                            break;
                        case 'PROCESSING':
                            $this['status'] = 'PROCESSING';
                            break;
                        case 'REFUNDED':
                            $this['status'] = 'FAIL';
                            break;
                    }
                } else {
                    $this['status'] = 'FAIL';
                }
            } else {
                $this['status'] = 'FAIL';
            }
            $this->save();
            if ($sendResponse) {
                return $this['status'];
            }
        }
    }

    public function discount_transaction(): HasOne
    {
        return $this->hasOne(AccountTransaction::class, 'topup_id');
    }
}
