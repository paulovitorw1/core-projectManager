<?php

namespace App\Interfaces\Services;

interface AuthServiceInterface
{
    public function create($user);

    public function getUser($token);
    public function login($credentials);

    public function logout();
}