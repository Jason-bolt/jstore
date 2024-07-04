<?php

namespace App\Interfaces;

use App\Http\Requests\UserLoginRequest;
use App\Models\User;

interface AuthServiceInterface {
    public function createUser(string $first_name, string $last_name, string $email, $password);
    public function resendOtp(string $email);
    public function verifyOtp(string $otp, string $email);
    public function login(string $email, string $password);
    public function logout();
    public function sendResetPassword(string $email);
}