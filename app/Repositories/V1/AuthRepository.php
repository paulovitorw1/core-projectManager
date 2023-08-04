<?php

namespace App\Repositories\V1;

use App\Interfaces\Repositories\AuthRepositoryInterface;
use App\Models\User;

class AuthRepository implements AuthRepositoryInterface {

    /**
     * Store a new user.
     *
     * @param  array $user
     * @return \Illuminate\Http\Response
     */
    public function create($user): User {
        return User::create($user);
    }
}