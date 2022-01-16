<?php

namespace App\Http\Services;

use App\Http\Interfaces\UsersServiceInterface;
use App\Models\User;

class UsersService implements UsersServiceInterface
{    
    /**
     * Get user by email
     *
     * @param  mixed $userEmail
     * @return void
     */
    public function getUserByEmail($userEmail)
    {
        return User::where('user_email', $userEmail)
               ->first();
    }
    
    /**
     * Get user by email and code
     *
     * @param  mixed $userEmail
     * @param  mixed $userVerificationCode
     * @return void
     */
    public function getUserByEmailAndCode($userEmail, $userVerificationCode)
    {
        return User::where('user_email', $userEmail)
                ->where('user_verification_code', $userVerificationCode)
                ->first();
    }
    
    /**
     * Get user by id
     *
     * @param  mixed $userId
     * @return void
     */
    public function getUserById($userId)
    {
        return User::where('user_id', $userId)
               ->first();
    }
    
    /**
     * Generate user verification code
     *
     * @return void
     */
    public function generateUserVerificationCode()
    {
        return bin2hex(openssl_random_pseudo_bytes(4));
    }
}