<?php

namespace App\Models;

use App\Enums\OTPValidationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\UUID;


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

    public function createValidOTP(string $userId, string $otp, $expireAt)
    {
        $this->where('user_id', $userId)->where('expire_at', '>', now())->update(['status' => OTPValidationStatus::INVALID]);

        return $this->create([
            'user_id' => $userId,
            'otp' => $otp,
            'expire_at' => $expireAt,
            'status' => OTPValidationStatus::VALID,
        ]);
    }

    public function validateOTP(array $data)
    {
        $otpRecord = $this->where('user_id', $data['userId'])
            ->where('otp', '=', $data['otp'])
            ->select('status', 'expire_at', 'id')
            ->first();

        if (!$otpRecord) {
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
        }
        return OTPValidationStatus::VALID;
    }

    public function updateOTPStatus(string $otpId, string $status)
    {
        $this->where('id', $otpId)
            ->update(['status' => $status]);
    }
}
