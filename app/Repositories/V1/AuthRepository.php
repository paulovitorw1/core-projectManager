<?php

namespace App\Repositories\V1;

use App\Interfaces\Repositories\AuthRepositoryInterface;
use App\Models\User;
use Illuminate\Support\Str;

class AuthRepository implements AuthRepositoryInterface
{

    /**
     * Store a new user.
     *
     * @param  array $user
     * @return \Illuminate\Http\Response
     */
    public function create($user): User
    {
        return User::create($user);
    }

    /**
     * Get the user by ID or credentials.
     *
     * @param mixed $identifier The user ID (UUID) or credentials (e.g., email/username and password).
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function getUserByIdOrCredentials($identifier): User | null
    {
        if (Str::isUuid($identifier)) {
            return User::where('id', $identifier)->first();
        }
        
        return User::where('email', $identifier)->first();
    }
}
