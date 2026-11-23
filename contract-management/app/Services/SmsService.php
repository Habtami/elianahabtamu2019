<?php

namespace App\Services;

use App\Models\SmsLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    protected ?string $apiUrl;
    protected ?string $apiKey;
    protected ?string $sender;

    public function __construct()
    {
        $this->apiUrl = config('services.sms.url');
        $this->apiKey = config('services.sms.api_key');
        $this->sender = config('services.sms.sender', 'EthioSMS');
    }

    public function isConfigured(): bool
    {
        return !empty($this->apiUrl) && !empty($this->apiKey);
    }

    public function sendSms(string $phone, string $message): ?SmsLog
    {
        $log = SmsLog::create([
            'phone' => $phone,
            'message' => $message,
            'status' => SmsLog::STATUS_PENDING,
        ]);

        if (!$this->isConfigured()) {
            $log->update(['status' => SmsLog::STATUS_FAILED, 'response' => 'SMS service not configured']);
            return $log;
        }

        try {
            $response = Http::post($this->apiUrl, [
                'api_key' => $this->apiKey,
                'sender' => $this->sender,
                'to' => $phone,
                'message' => $message,
            ]);

            if ($response->successful()) {
                $log->update(['status' => SmsLog::STATUS_SENT, 'response' => $response->body()]);
            } else {
                $log->update(['status' => SmsLog::STATUS_FAILED, 'response' => $response->body()]);
            }

            return $log;
        } catch (\Exception $e) {
            $log->update(['status' => SmsLog::STATUS_FAILED, 'response' => $e->getMessage()]);
            Log::error('SMS send error', ['exception' => $e->getMessage()]);
            return $log;
        }
    }
}