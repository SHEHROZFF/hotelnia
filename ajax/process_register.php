<?php
// Prevent PHP errors from being included in output
ini_set('display_errors', 0);
error_reporting(0);

// Start output buffering to capture any unexpected output
ob_start();

// Set content type to JSON before any output
header('Content-Type: application/json');

try {
    // Include global settings
    require_once '../global.php';

    // Check if the form is submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Get form data
        $name = isset($_POST['name']) ? trim($_POST['name']) : '';
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        $confirmPassword = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
        
        // Validate input
        $errors = [];
        
        if (empty($name)) {
            $errors['name'] = 'Name is required';
        }
        
        if (empty($email)) {
            $errors['email'] = 'Email is required';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Invalid email format';
        }
        
        if (empty($password)) {
            $errors['password'] = 'Password is required';
        } elseif (strlen($password) < 8) {
            $errors['password'] = 'Password must be at least 8 characters';
        }
        
        if (empty($confirmPassword)) {
            $errors['confirm_password'] = 'Please confirm your password';
        } elseif ($password !== $confirmPassword) {
            $errors['confirm_password'] = 'Passwords do not match';
        }
        
        // If no validation errors, attempt to register
        if (empty($errors)) {
            $user = new User($db, $security);
            $result = $user->register($name, $email, $password);
            
            // Clear any unexpected output from buffer before sending JSON
            ob_clean();
            
            if ($result['success']) {
                // Registration is successful but now requires email verification
                echo json_encode([
                    'success' => true,
                    'message' => 'Registration successful. Please check your email to verify your account.',
                    'verification_required' => true,
                    'redirect' => $urlval . 'Login.php'
                ]);
                exit;
            } else {
                // Registration failed
                echo json_encode([
                    'success' => false,
                    'message' => $result['message']
                ]);
                exit;
            }
        } else {
            // Clear any unexpected output from buffer before sending JSON
            ob_clean();
            
            // Return validation errors
            echo json_encode([
                'success' => false,
                'errors' => $errors
            ]);
            exit;
        }
    } else {
        // Clear any unexpected output from buffer before sending JSON
        ob_clean();
        
        // Not a POST request
        echo json_encode([
            'success' => false,
            'message' => 'Invalid request method'
        ]);
        exit;
    }
} catch (Exception $e) {
    // Clear any unexpected output from buffer before sending JSON
    ob_clean();
    
    // Catch any unexpected errors and return as JSON
    error_log("Registration error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'An unexpected error occurred. Please try again.'
    ]);
    exit;
}
?> 