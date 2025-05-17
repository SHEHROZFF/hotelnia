<?php include "header.php" ?>

<?php
// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: ' . $urlval);
    exit;
}

// Check for verification result message
$verificationMessage = '';
$verificationClass = '';
if (isset($_SESSION['verification_result'])) {
    $result = $_SESSION['verification_result'];
    $verificationMessage = $result['message'];
    $verificationClass = $result['success'] ? 'alert-success' : 'alert-danger';
    unset($_SESSION['verification_result']); // Clear the message after showing it
}

// Handle resend verification request
if (isset($_POST['resend_verification'])) {
    $email = $_POST['email'];
    $user = new User($db, $security);
    $result = $user->resendVerificationEmail($email);
    $verificationMessage = $result['message'];
    $verificationClass = $result['success'] ? 'alert-success' : 'alert-danger';
}
?>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center text-white">
                    <h1 class="display-4 fw-bold">Login
                    </h1>
                </div>
            </div>
        </div>
    </section>
    <div class="container">
        <div class="row d-flex align-items-center justify-content-center">
    <div class="login-container">
        <h1 class="login-title">Sign in to Your Account</h1>
        
        <!-- Display verification messages -->
        <?php if (!empty($verificationMessage)): ?>
        <div class="alert <?php echo $verificationClass; ?> mt-3">
            <?php echo $verificationMessage; ?>
        </div>
        <?php endif; ?>
        
        <!-- Alert for error/success messages -->
        <div id="message" class="alert" style="display: none;"></div>
        
        <form id="loginForm" method="post">
            <div class="mb-3">
                <label for="email" class="form-label">Your Email</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Enter Your Email">
                <div class="invalid-feedback" id="email-error"></div>
            </div>
            
            <div class="mb-3">
                <label for="password" class="form-label">Your Password</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Password">
                <div class="invalid-feedback" id="password-error"></div>
            </div>
            
            <div class="form-check mb-3">
                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                <label class="form-check-label" for="remember">Remember me</label>
            </div>
            
            <button type="submit" class="btn btn-primary w-100">Login</button>
            
            <div class="signup-text mt-3">
                Don't have an account? <a href="register.php" class="signup-link">Sign Up</a> for free
            </div>
        </form>
        
        <!-- Resend verification email form -->
        <div class="mt-4 pt-3 border-top">
            <h5 class="mb-2">Didn't receive verification email?</h5>
            <form method="post" class="d-flex gap-2 align-items-stretch">
                <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
                <input type="hidden" name="resend_verification" value="1">
                <button type="submit" class="btn btn-primary h-100">Resend</button>
            </form>
        </div>

    </div>
</div>
</div>

<script src="assets/js/login.js"></script>

<?php include "footer.php" ?>