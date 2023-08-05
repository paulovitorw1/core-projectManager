<?php

namespace App\Enums;

class OTPValidationStatus
{
    const VALID = 'valid';
    const INVALID = 'invalid';
    const EXPIRED = 'expired';
    const USED = 'Used';
}