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
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        $remember = isset($_POST['remember']) ? (bool)$_POST['remember'] : false;
        
        // Validate input
        $errors = [];
        
        if (empty($email)) {
            $errors['email'] = 'Email is required';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Invalid email format';
        }
        
        if (empty($password)) {
            $errors['password'] = 'Password is required';
        }
        
        // If no validation errors, attempt to login
        if (empty($errors)) {
            $user = new User($db, $security);
            $result = $user->login($email, $password, $remember);
            
            if ($result['success']) {
                // Clear any unexpected output from buffer before sending JSON
                ob_clean();
                
                // Login successful
                echo json_encode([
                    'success' => true,
                    'message' => 'Login successful. Redirecting...',
                    'redirect' => $urlval // Redirect to homepage
                ]);
                exit;
            } else {
                // Clear any unexpected output from buffer before sending JSON
                ob_clean();
                
                // Login failed
                $responseData = [
                    'success' => false,
                    'message' => $result['message']
                ];
                
                // Add verification status if available
                if (isset($result['verified']) && $result['verified'] === false) {
                    $responseData['verification_required'] = true;
                }
                
                echo json_encode($responseData);
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
    error_log("Login error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'An unexpected error occurred. Please try again.'
    ]);
    exit;
}
?> 