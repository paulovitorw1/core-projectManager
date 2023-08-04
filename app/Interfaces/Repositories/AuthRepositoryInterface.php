<?php

namespace App\Interfaces\Repositories;

use App\Models\User;

interface AuthRepositoryInterface
{
    public function create(array $user): User;
}