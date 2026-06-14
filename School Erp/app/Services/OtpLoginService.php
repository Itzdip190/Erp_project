<?php

namespace App\Services;

use App\Models\OtpLogin;
use Illuminate\Support\Facades\Log;

class OtpLoginService
{
    public function generateOtp(int $userId, int $schoolId, string $phone): OtpLogin
    {
        OtpLogin::where('user_id', $userId)->whereNull('used_at')->delete(); // invalidate old OTPs
        
        return OtpLogin::create([
            'school_id'  => $schoolId,
            'user_id'    => $userId,
            'phone'      => $phone,
            'otp'        => str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT),
            'expires_at' => now()->addMinutes(5),
        ]);
    }

    public function sendOtp(string $phone, string $otp, array $smsConfig): void
    {
        if (app()->environment('local', 'testing')) {
            Log::info("OTP for {$phone}: {$otp}");
            return;
        }
        
        // Mock dispatch or log for production gateway integration
        Log::info("OTP dispatched to {$phone}: {$otp} using config: " . json_encode($smsConfig));
    }

    public function verifyOtp(int $schoolId, string $phone, string $otp): ?OtpLogin
    {
        $record = OtpLogin::where('school_id', $schoolId)
            ->where('phone', $phone)
            ->where('otp', $otp)
            ->where('expires_at', '>', now())
            ->whereNull('used_at')
            ->first();

        if (!$record) return null;

        $record->update(['used_at' => now()]);
        return $record;
    }
}
