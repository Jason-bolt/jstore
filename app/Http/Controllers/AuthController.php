<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\ResendOtpRequest;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\VerifyOtpRequest;
use App\Http\Resources\GenericResponseResource;
use App\Interfaces\AuthServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    private $authService;
    function __construct(AuthServiceInterface $authService)
    {
        $this->authService = $authService;
    }

    public function createUser(CreateUserRequest $request)
    {
        try {
            $first_name = $request->input("first_name");
            $last_name = $request->input("last_name");
            $email = $request->input("email");
            $password = $request->input("password");

            Log::info("User information $request");

            DB::beginTransaction();
            $createdUser = $this->authService->createUser($first_name, $last_name, $email, $password);

            DB::commit();
            
            return new GenericResponseResource($createdUser, config('Constants.httpStatusCodes.CREATED'), "User created successfully!");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Exception occurred while creating user " . $e->getMessage());
            return response()->json(['message' => 'Internal server error. ' . $e->getMessage()], config('Constants.httpStatusCodes.INTERNAL_SERVER_ERROR'));
        }
    }
    
    public function resendOtp(ResendOtpRequest $request)
    {
        try {
            $email = $request->input("email");

            Log::info("User information $request");

            DB::beginTransaction();
            $NewOtp = $this->authService->resendOtp($email);

            DB::commit();
            
            return new GenericResponseResource($NewOtp, config('Constants.httpStatusCodes.OK'), "User otp resent!");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Exception occurred while resending user otp " . $e->getMessage());
            return response()->json(['message' => 'Internal server error. ' . $e->getMessage()], config('Constants.httpStatusCodes.INTERNAL_SERVER_ERROR'));
        }
    }
    
    public function verifyOtp(VerifyOtpRequest $request)
    {
        try {
            $email = $request->input("email");
            $otp = $request->input("otp");

            Log::info("User information $request");

            DB::beginTransaction();
            $user = $this->authService->verifyOtp($otp, $email);

            DB::commit();

            if (!$user) {
                return new GenericResponseResource(null, config('Constants.httpStatusCodes.BAD_REQUEST'), "OTP provided was wrong!");
            }
            return new GenericResponseResource($user, config('Constants.httpStatusCodes.OK'), "User verified successfully!");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Exception occurred while verifying user " . $e->getMessage());
            return response()->json(['message' => 'Internal server error. ' . $e->getMessage()], config('Constants.httpStatusCodes.INTERNAL_SERVER_ERROR'));
        }
    }
    
    public function login(UserLoginRequest $request)
    {
        try {
            $email = $request->input("email");
            $password = $request->input("password");

            Log::info("User information $request");

            DB::beginTransaction();
            $user = $this->authService->login($email, $password);

            DB::commit();

            if (!$user) {
                return new GenericResponseResource(null, config('Constants.httpStatusCodes.BAD_REQUEST'), "Email or password is incorect!");
            }
            return new GenericResponseResource($user, config('Constants.httpStatusCodes.OK'), "User logged in successfully!");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Exception occurred while logging in user " . $e->getMessage());
            return response()->json(['message' => 'Internal server error. ' . $e->getMessage()], config('Constants.httpStatusCodes.INTERNAL_SERVER_ERROR'));
        }
    }
    
    public function logout(Request $request)
    {
        try {       
            Log::info("User information $request");
            
            DB::beginTransaction();
            $this->authService->logout();

            DB::commit();

            return new GenericResponseResource(null, config('Constants.httpStatusCodes.OK'), "User logged out successfully!");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Exception occurred while logging out user " . $e->getMessage());
            return response()->json(['message' => 'Internal server error. ' . $e->getMessage()], config('Constants.httpStatusCodes.INTERNAL_SERVER_ERROR'));
        }
    }
    
    public function sendResetPassword(ResendOtpRequest $request)
    {
        try {       
            $email = $request->input("email");

            Log::info("User information $request");
            
            DB::beginTransaction();
            $mailInfo = $this->authService->sendResetPassword($email);

            DB::commit();

            return new GenericResponseResource($mailInfo, config('Constants.httpStatusCodes.OK'), "Reset password mail sent successfully!");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Exception occurred while logging out user " . $e->getMessage());
            return response()->json(['message' => 'Internal server error. ' . $e->getMessage()], config('Constants.httpStatusCodes.INTERNAL_SERVER_ERROR'));
        }
    }
    
    public function unauthorized()
    {
        return new GenericResponseResource(null, config('Constants.httpStatusCodes.FORBIDDEN'), "Unauthorized!");
    }
}
