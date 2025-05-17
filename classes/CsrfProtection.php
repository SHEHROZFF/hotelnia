<?php

class CsrfProtection {
    public static function generateToken() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start(); 
        }

        $token = bin2hex(random_bytes(32));  
        $_SESSION['csrf_token'] = $token;   
        return $token;
    }

    public static function validateToken($token) {
        if (session_status() == PHP_SESSION_NONE) {
            session_start(); 
        }
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);  // Secure token comparison
    }
}
