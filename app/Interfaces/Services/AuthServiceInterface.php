<?php

namespace App\Interfaces\Services;

interface AuthServiceInterface {
    public function getUser($token);
    public function login($credentials);

    public function logout();

}
