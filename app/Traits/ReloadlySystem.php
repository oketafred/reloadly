<?php

namespace App\Traits;

use App\Models\Log;
use App\Models\Operator;
use Illuminate\Support\Facades\Http;
use OTIFSolutions\Laravel\Settings\Models\Setting;

trait ReloadlySystem
{
    public function getReloadlyApiUrlAttribute(): string
    {
        return Setting::get('reloadly_api_mode') ? 'https://topups.reloadly.com' : 'https://topups-sandbox.reloadly.com';
    }

    public function getReloadlyGiftApiUrlAttribute(): string
    {
        return Setting::get('reloadly_api_mode') ? 'https://giftcards.reloadly.com' : 'https://giftcards-sandbox.reloadly.com';
    }

    public function getToken()
    {
        $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post('https://auth.reloadly.com/oauth/token', [
                'client_id' => Setting::get('reloadly_api_key'),
                'client_secret' => Setting::get('reloadly_api_secret'),
                'grant_type' => 'client_credentials',
                'audience' => $this['reloadly_api_url'],
        ]);

        Log::query()->create([
            'task' => 'GET_TOKEN',
            'params' => '',
            'response' => $response->json(),
        ]);

        return $response->json('access_token');
    }

    public function getCountries($iso = null)
    {
        $url = $this['reloadly_api_url'] . '/countries' . ($iso ? '/' . $iso : '');

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . Setting::get('reloadly_api_token'),
        ])->get($url);

        Log::query()->create([
            'task' => 'GET_COUNTRIES',
            'params' => '',
            'response' => $response->json(),
        ]);

        return $response->json();
    }

    public function getOperators($page = 1)
    {
        $url = $this['reloadly_api_url'] . "/operators?page=$page&size=200&includeBundles=true&includeData=true&includePin=true&simplified=false&suggestedAmounts=true&suggestedAmountsMap=true";

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . Setting::get('reloadly_api_token'),
        ])->get($url);

        Log::query()->create([
            'task' => 'GET_OPERATORS',
            'params' => '',
            'response' => $response->json(),
        ]);

        return $response->json();
    }

    public function getOperatorsDiscount($page = 1)
    {
        $url = $this['reloadly_api_url'] . "/operators/commissions?page=$page&size=200&includeBundles=true&includeData=true&includePin=true&simplified=false&suggestedAmounts=true&suggestedAmountsMap=true";

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . Setting::get('reloadly_api_token'),
        ])->get($url);

        Log::query()->create([
            'task' => 'GET_OPERATORS_DISCOUNTS',
            'params' => '',
            'response' => $response->json(),
        ]);

        return $response->json();
    }

    public function getBalance(): string
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . Setting::get('reloadly_api_token'),
        ])->get($this['reloadly_api_url'] . '/accounts/balance');

        if ($response->successful()) {
            $data = $response->json();

            if (isset($data['currencyCode'])) {
                Setting::set('reloadly_currency', $data['currencyCode']);
                $this->save();
            }

            return isset($data['balance'], $data['currencyCode']) ? $data['balance'] . ' ' . $data['currencyCode'] : '---';
        }

        return '---';
    }

    public function autoDetectOperator($phone, $iso, $fileId)
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . Setting::get('reloadly_api_token'),
        ])->get($this['reloadly_api_url'] . "/operators/auto-detect/phone/$phone/country-code/" . $iso . '?&includeBundles=true');

        Log::query()->create([
            'task' => 'AUTO_DETECT',
            'params' => ' FILE:' . $fileId,
            'response' => $response->json(),
        ]);

        $data = $response->json();

        return isset($data['operatorId']) ? Operator::query()->where('rid', $data['operatorId'])->first() : null;
    }

    public function getPromotions($page = 1)
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . Setting::get('reloadly_api_token'),
        ])->get($this['reloadly_api_url'] . "/promotions?page=$page");

        Log::query()->create([
            'task' => 'GET_PROMOTIONS',
            'params' => '',
            'response' => $response->json(),
        ]);

        return $response->json();
    }

    public function getGiftTokenAttribute()
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post('https://auth.reloadly.com/oauth/token', [
            'client_id' => Setting::get('reloadly_api_key'),
            'client_secret' => Setting::get('reloadly_api_secret'),
            'grant_type' => 'client_credentials',
            'audience' => $this['reloadly_gift_api_url'],
        ]);

        return $response['access_token'] ?? null;
    }

    public function getReloadlyGiftProducts($page = 1)
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $this['gift_token'],
        ])->get($this['reloadly_gift_api_url'] . '/products', [
            'page' => $page,
            'size' => 200,
        ]);

        return $response->json();
    }

    public function orderReloadlyGiftProducts($rid, $iso, $quantity, $price, $identifier, $senderName, $email)
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $this['gift_token'],
        ])->post($this['reloadly_gift_api_url'] . '/orders', [
            'productId' => $rid,
            'countryCode' => $iso,
            'quantity' => $quantity,
            'unitPrice' => $price,
            'customIdentifier' => $identifier,
            'senderName' => $senderName,
            'recipientEmail' => $email,
        ]);

        return $response->json();
    }
}
