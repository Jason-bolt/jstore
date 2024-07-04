<?php

namespace App\Services;

use App\Helpers\GenericHelpers;
use App\Interfaces\AuthServiceInterface;
use App\Jobs\ResendMailJob;
use App\Jobs\SendResetPasswordMailJob;
use App\Jobs\UserCreatedJob;
use App\Models\ResetPasswordOtp;
use App\Models\User;
use App\Models\UserOtp;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class AuthService implements AuthServiceInterface {
    public function createUser(string $first_name, string $last_name, string $email, $password)
    {
        $generated_otp = GenericHelpers::generateOtp();

        $user = User::create([
            "first_name" => $first_name,
            "last_name" => $last_name,
            "email" => $email,
            "password" => Hash::make($password),
        ]);

        UserOtp::create([
            "user_id" => $user->id,
            "otp" => GenericHelpers::jwtEncode($generated_otp)
        ]);

        dispatch(new UserCreatedJob($user->email, $generated_otp));

        return [
            "user" => $user,
            "otp" => $generated_otp
        ];
    }

    public function resendOtp(string $email)
    {
        $generated_otp = GenericHelpers::generateOtp();
        $user = User::where("email", $email)->first();
        UserOtp::where("user_id", $user->id)->delete();
        UserOtp::create([
            "user_id"=> $user->id,
            "otp" => GenericHelpers::jwtEncode($generated_otp)
        ]);
        
        dispatch(new ResendMailJob($user->email, $generated_otp));

        return [
            "user" => $user,
            "otp" => $generated_otp
        ];
    }
    
    public function verifyOtp(string $otp, string $email)
    {
        $user = User::where("email", $email)->first();
        $userOtp = $user->otp;
        $decryptedOtp = GenericHelpers::decodeJwt($userOtp->otp);
        if ($decryptedOtp["data"] !== $otp)
        {
            return false;
        }

        UserOtp::where("user_id", $user->id)->delete();
        $user->update([
            "status" => config("Enums.user.USER_STATUS.ACTIVE"),
            "email_verified_at" => now()
            ]);

        return $user->refresh();
    }

    public function login(string $email, string $password)
    {
        if (!auth()->attempt(["email"=> $email,"password"=> $password])) {
            return false;
        }

        return [
            "id" => auth()->id(),
            "token" => auth()->user()->createToken("admin")->plainTextToken,
        ];
    }
    
    public function logout()
    {
        $user = auth()->user();
        $user->currentAccessToken()->delete();

        return;
    }
    
    public function sendResetPassword(string $email)
    {
        $generated_otp = GenericHelpers::generateOtp();
        $user = User::where("email", $email)->first();
        ResetPasswordOtp::where("user_id", $user->id)->delete();
        ResetPasswordOtp::create([
            "user_id"=> $user->id,
            "otp" => GenericHelpers::jwtEncode($generated_otp)
        ]);
        
        dispatch(new SendResetPasswordMailJob($user->email, $generated_otp));

        return [
            "user" => $user,
            "otp" => $generated_otp
        ];
    }
}