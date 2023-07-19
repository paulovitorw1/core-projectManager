<?php

namespace App\Repositories\V1;

use App\Models\User;

class AuthRepository {
    /**
     * Store a new user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create($user)  {
        return User::create($user);
    }
}