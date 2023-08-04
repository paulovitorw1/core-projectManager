<?php

namespace App\Services\Api\V1;

use App\Interfaces\Services\OTPServiceInterface;

class OTPService implements OTPServiceInterface
{
    /**
     * Create a new OTP Code.
     *
     * @param int $length The size of the otp code.
     * @return string return a string contain o otp code created.
     */
    public function generateOTP($length = 6): string {
        $otp = '';
        $characters = '0123456789';
        $max = strlen($characters) - 1;
        
        try {
            // Gera bytes aleatórios seguros
            $randomBytes = random_bytes($length);
            
            for ($i = 0; $i < $length; $i++) {
                $otp .= $characters[ord($randomBytes[$i]) % $max];
            }
        } catch (\Exception $e) {
            for ($i = 0; $i < $length; $i++) {
                $otp .= $characters[rand(0, $max)];
            }
        }
        
        return $otp;
    }   
}
