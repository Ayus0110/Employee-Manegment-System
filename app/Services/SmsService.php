<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    public function sendSms($phone, $message)
    {
        $response = Http::withHeaders([
            'authorization' => env('G1vp2wqVvyrfja64a3GXFFCPHePp9TPIPEbxHWday6QgevkAqTAPYNgUNL5F'),
        ])->post('https://www.fast2sms.com/dev/bulkV2', [
            'message' => $message,
            'language' => 'english',
            'route' => 'q',
            'numbers' => $phone,
        ]);

        Log::info('FAST2SMS RESPONSE', [
            'phone' => $phone,
            'message' => $message,
            'status_code' => $response->status(),
            'response' => $response->body(),
        ]);

        return $response;
    }
}
