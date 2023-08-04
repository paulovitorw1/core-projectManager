<?php

namespace App\Models;

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
        $this->where('user_id', $userId)->where('expire_at', '>', now())->update(['deleted_at' => now()]);

        return $this->create([
            'user_id' => $userId,
            'otp' => $otp,
            'expire_at' => $expireAt,
        ]);
    }
}
