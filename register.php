<?php include "header.php" ?>

<?php
// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: ' . $urlval);
    exit;
}
?>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center text-white">
                    <h1 class="display-4 fw-bold">Register
                    </h1>
                </div>
            </div>
        </div>
    </section>
    <div class="container">
        <div class="row d-flex align-items-center justify-content-center">
            <div class="register-container">
                <h1 class="register-title text-center">Register Your Account</h1>
                
                <!-- Alert for error/success messages -->
                <div id="message" class="alert" style="display: none;"></div>
                
                <form id="registerForm" method="post">
                    <div class="mb-3">
                        <label for="name" class="form-label">Your Name</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Enter Your Name">
                        <div class="invalid-feedback" id="name-error"></div>
                    </div>
                    
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
                    
                    <div class="mb-3">
                        <label for="confirm-password" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" id="confirm-password" name="confirm_password" placeholder="Confirm Password">
                        <div class="invalid-feedback" id="confirm-password-error"></div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100">Register</button>
                    
                    <div class="login-text text-center">
                        You have an account? <a href="Login.php" class="login-link">Login</a>
                    </div>
                </form>
            </div>
</div>
</div>

<script src="assets/js/register.js"></script>

<?php include "footer.php" ?>