<?php

namespace App\Models;

use App\Mail\GenericMailer;
use App\Traits\PaypalSystem;
use App\Traits\StripeSystem;
use Illuminate\Support\Facades\Mail;
use Illuminate\Database\Eloquent\Model;
use OTIFSolutions\Laravel\Settings\Models\Setting;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class System extends Model
{
    use HasFactory;
    use StripeSystem, PaypalSystem;

    public static function sendEmail($to, $view, $data)
    {
        try {
            Mail::to($to)->send(new GenericMailer($data, $view));
        } catch (\Exception $ex) {

        }
    }

    public static function autoDetectOperator($phone, $iso, $fileId)
    {
        $system = User::admin();
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $system['reloadly_api_url'] . "/operators/auto-detect/phone/$phone/country-code/" . $iso . '?&includeBundles=true');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type:application/json',
            'Authorization: Bearer ' . Setting::get('reloadly_api_token'),
        ]);

        $response = curl_exec($ch);
        curl_close($ch);
        \App\Models\Log::create([
            'task' => 'AUTO_DETECT',
            'params' => ' FILE:' . $fileId,
            'response' => $response,
        ]);
        $response = \GuzzleHttp\json_decode($response);

        return isset($response->operatorId) ? Operator::where('rid', $response->operatorId)->first() : null;
    }
}
