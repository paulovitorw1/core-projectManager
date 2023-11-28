<?php

namespace App\Models;

use App\Enums\OTPValidationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\UUID;
use Carbon\Carbon;

class VerificationCode extends Model
{
    use HasFactory;
    use UUID;

    protected $fillable = [
        'user_id',
        'otp',
        'expire_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function validateOTP(array $data)
    {
        $otpRecord = $this->where('user_id', $data['user_id'])
            ->where('otp', '=', $data['otp'])
            ->select('status', 'expire_at', 'id')
            ->first();

        if (!$otpRecord || $otpRecord->status === OTPValidationStatus::INVALID) {
            return OTPValidationStatus::INVALID;
        }

        if ($otpRecord->status === OTPValidationStatus::USED) {
            return OTPValidationStatus::USED;
        }

        if ($otpRecord->expire_at < now()) {
            $this->updateOTPStatus($otpRecord->id, OTPValidationStatus::EXPIRED);
            return OTPValidationStatus::EXPIRED;
        }

        if ($otpRecord->status === OTPValidationStatus::VALID) {
            $this->updateOTPStatus($otpRecord->id, OTPValidationStatus::USED);
            $this->updateStatusUser($data['user_id']);
        }
        
        return OTPValidationStatus::VALID;
    }

    private function updateOTPStatus(string $otpId, string $status)
    {
        $this->where('id', $otpId)
            ->update(['status' => $status]);
    }

    private function updateStatusUser(string $userId) {
        $user = User::find($userId);
        $user->email_verified_at = now();
        $user->save();
    }
}
