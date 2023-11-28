<?php

namespace App\Interfaces\Services;

use App\Models\User;

interface AuthServiceInterface
{
    public function create(array $user);
    public function getUser($token);
    public function login($credentials);
    public function logout();
    public function validateOTP(array $data);
    public function sendEmailWithOTP(array $data);
    public function getUserCredentials($credentials): User | null;

}