<?php
class User {
    private $db;
    private $security;

    public function __construct($database, $security = null) {
        $this->db = $database->getConnection();
        $this->security = $security;
    }

    public function getUser($id) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getUserByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function register($name, $email, $password) {
        // Check if email already exists
        if ($this->getUserByEmail($email)) {
            return [
                'success' => false,
                'message' => 'Email already registered'
            ];
        }

        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        // Generate verification token
        $verificationToken = bin2hex(random_bytes(32));
        $tokenExpiresAt = date('Y-m-d H:i:s', strtotime('+24 hours'));
        
        // Insert new user with verification token
        $stmt = $this->db->prepare("INSERT INTO users (name, email, password, created_at, email_verified, verification_token, token_expires_at) 
                                  VALUES (:name, :email, :password, :created_at, :email_verified, :verification_token, :token_expires_at)");
        $created_at = date('Y-m-d H:i:s');
        $email_verified = 0; // Default to not verified
        
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':created_at', $created_at);
        $stmt->bindParam(':email_verified', $email_verified);
        $stmt->bindParam(':verification_token', $verificationToken);
        $stmt->bindParam(':token_expires_at', $tokenExpiresAt);
        
        if ($stmt->execute()) {
            $userId = $this->db->lastInsertId();
            
            // Send verification email
            $this->sendVerificationEmail($email, $name, $verificationToken);
            
            return [
                'success' => true,
                'message' => 'Registration successful. Please check your email to verify your account.',
                'user_id' => $userId,
                'verification_required' => true
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Registration failed. Please try again.'
            ];
        }
    }

    private function sendVerificationEmail($email, $name, $token) {
        // Get the base URL
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        
        // Add the base directory path /hotelina
        $baseUrl = $protocol . '://' . $host . '/hotelina';
        
        // Create verification link
        $verificationLink = $baseUrl . '/verify.php?token=' . $token;
        
        // Email content
        $subject = 'Verify Your Email - Hotelina';
        $message = '
        <html>
        <head>
            <title>Verify Your Email</title>
        </head>
        <body>
            <div style="max-width: 600px; margin: 0 auto; padding: 20px; font-family: Arial, sans-serif;">
                <div style="background-color: #f0f0f0; padding: 20px; text-align: center;">
                    <h1 style="color: #333;">Verify Your Email</h1>
                </div>
                <div style="padding: 20px; background-color: #fff;">
                    <p>Dear '.$name.',</p>
                    <p>Thank you for registering with Hotelina. Please click the link below to verify your email address:</p>
                    <p style="text-align: center;">
                        <a href="'.$verificationLink.'" style="display: inline-block; padding: 10px 20px; background-color: #029bfe; color: #fff; text-decoration: none; border-radius: 5px;">Verify Email</a>
                    </p>
                    <p>If the button above doesn\'t work, copy and paste the following link into your browser:</p>
                    <p>'.$verificationLink.'</p>
                    <p>This link will expire in 24 hours.</p>
                    <p>If you did not create an account, you can ignore this email.</p>
                    <p>Best regards,<br>The Hotelina Team</p>
                </div>
                <div style="background-color: #333; color: #fff; padding: 15px; text-align: center;">
                    <p>&copy; '.date('Y').' Hotelina. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>';
        
        // Send email using the smtp_mailer function
        include_once(__DIR__ . '/../email/email.php');
        $result = smtp_mailer($email, $subject, $message);
        
        return $result === 'sent';
    }
    
    public function verifyEmail($token) {
        // Find user with this token
        $stmt = $this->db->prepare("SELECT * FROM users WHERE verification_token = :token AND token_expires_at > NOW()");
        $stmt->bindParam(':token', $token);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            // Update user as verified
            $updateStmt = $this->db->prepare("UPDATE users SET email_verified = 1, verification_token = NULL, token_expires_at = NULL WHERE id = :id");
            $updateStmt->bindParam(':id', $user['id']);
            
            if ($updateStmt->execute()) {
                return [
                    'success' => true,
                    'message' => 'Email verified successfully. You can now login.',
                    'user_id' => $user['id']
                ];
            }
        }
        
        return [
            'success' => false,
            'message' => 'Invalid or expired verification token.'
        ];
    }

    public function login($email, $password, $remember = false) {
        // Get user by email
        $user = $this->getUserByEmail($email);
        
        if (!$user) {
            return [
                'success' => false,
                'message' => 'Invalid email or password'
            ];
        }
        
        // Check if email is verified
        if (!$user['email_verified'] && isset($user['verification_token'])) {
            return [
                'success' => false,
                'message' => 'Please verify your email before logging in.',
                'verified' => false
            ];
        }
        
        // Verify password
        if (password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role']; // Add role to session
            
            // Set remember me cookie if requested
            if ($remember) {
                $token = bin2hex(random_bytes(32));
                $expires = time() + (86400 * 30); // 30 days
                
                // Store token in database
                $stmt = $this->db->prepare("UPDATE users SET remember_token = :token WHERE id = :id");
                $stmt->bindParam(':token', $token);
                $stmt->bindParam(':id', $user['id']);
                $stmt->execute();
                
                // Set cookie with secure and httpOnly flags
                $path = '/';
                $domain = '';
                $secure = isset($_SERVER['HTTPS']);
                $httpOnly = true;
                
                setcookie('remember_token', $token, [
                    'expires' => $expires,
                    'path' => $path,
                    'domain' => $domain,
                    'secure' => $secure,
                    'httponly' => $httpOnly,
                    'samesite' => 'Lax'
                ]);
                
                setcookie('user_id', $user['id'], [
                    'expires' => $expires,
                    'path' => $path,
                    'domain' => $domain,
                    'secure' => $secure,
                    'httponly' => $httpOnly,
                    'samesite' => 'Lax'
                ]);
            }
            
            return [
                'success' => true,
                'message' => 'Login successful',
                'user' => $user,
                'role' => $user['role'] // Add role to response
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Invalid email or password'
            ];
        }
    }

    public function logout() {
        // Destroy the session
        session_unset();
        session_destroy();
        
        // Clear remember me cookies
        if (isset($_COOKIE['remember_token'])) {
            $path = '/'; // Cookie available across entire domain
            $domain = ''; // Current domain only
            $secure = isset($_SERVER['HTTPS']); // True if using HTTPS
            $httpOnly = true; // Prevent JavaScript access
            
            // Set cookies with past expiration to delete them
            setcookie('remember_token', '', [
                'expires' => time() - 3600,
                'path' => $path,
                'domain' => $domain,
                'secure' => $secure,
                'httponly' => $httpOnly,
                'samesite' => 'Lax'
            ]);
            
            setcookie('user_id', '', [
                'expires' => time() - 3600,
                'path' => $path,
                'domain' => $domain,
                'secure' => $secure,
                'httponly' => $httpOnly,
                'samesite' => 'Lax'
            ]);
        }
        
        return true;
    }

    public function isLoggedIn() {
        if (isset($_SESSION['user_id'])) {
            return true;
        }
        
        // Check for remember me cookie
        if (isset($_COOKIE['remember_token']) && isset($_COOKIE['user_id'])) {
            $token = $_COOKIE['remember_token'];
            $userId = $_COOKIE['user_id'];
            
            $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id AND remember_token = :token");
            $stmt->bindParam(':id', $userId);
            $stmt->bindParam(':token', $token);
            $stmt->execute();
            
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                
                return true;
            }
        }
        
        return false;
    }
    
    public function resendVerificationEmail($email) {
        // Get user by email
        $user = $this->getUserByEmail($email);
        
        if (!$user) {
            return [
                'success' => false,
                'message' => 'Email not found.'
            ];
        }
        
        // Check if already verified
        if ($user['email_verified']) {
            return [
                'success' => false,
                'message' => 'Email already verified.'
            ];
        }
        
        // Generate new verification token
        $verificationToken = bin2hex(random_bytes(32));
        $tokenExpiresAt = date('Y-m-d H:i:s', strtotime('+24 hours'));
        
        // Update token in database
        $stmt = $this->db->prepare("UPDATE users SET verification_token = :token, token_expires_at = :expires WHERE id = :id");
        $stmt->bindParam(':token', $verificationToken);
        $stmt->bindParam(':expires', $tokenExpiresAt);
        $stmt->bindParam(':id', $user['id']);
        
        if ($stmt->execute()) {
            // Send verification email
            $emailSent = $this->sendVerificationEmail($email, $user['name'], $verificationToken);
            
            if ($emailSent) {
                return [
                    'success' => true,
                    'message' => 'Verification email sent. Please check your inbox.'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to send verification email. Please try again.'
                ];
            }
        }
        
        return [
            'success' => false,
            'message' => 'Failed to generate new verification token. Please try again.'
        ];
    }
}
?>
