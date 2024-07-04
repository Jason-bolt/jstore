<?php

namespace App\Helpers;

use Exception;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;

class GenericHelpers {
    public static function generateOtp(int $length = 4): string 
    {
        $otp = '';
        for ($i = 0; $i < $length; $i++) {
            $otp .= random_int(0, 9);
        }
        
        return $otp;
    }

    public static function jwtEncode($payload, $expiry = 24): string
    {
        $issuedAt = time();
        $expirationTime = $issuedAt + (3600 * $expiry);
    
        $tokenPayload = array(
            "iat" => $issuedAt,
            "exp" => $expirationTime,
            "data" => $payload
        );
    
        // Encode the payload using the secret key and specified algorithm
        $jwt = JWT::encode($tokenPayload, env("JWT_SECRET"), 'HS256');
    
        return $jwt;
    }
    
    public static function decodeJwt($jwt): array
    {
        try {
            $decoded = JWT::decode($jwt, new Key(env("JWT_SECRET"), 'HS256'));
            // Convert the decoded object to an associative array
            return (array) $decoded;
        } catch (ExpiredException $e) {
            // Handle expired token
            return ['error' => 'Token has expired'];
        } catch (SignatureInvalidException $e) {
            // Handle invalid token signature
            return ['error' => 'Token signature is invalid'];
        } catch (Exception $e) {
            // Handle other exceptions
            return ['error' => 'An error occurred while decoding the token - ' . $e->getMessage()];
        }
    }
}