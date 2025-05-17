<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login | Control Panel</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">


    <style>
    body {
        background-color: #f4f6f9;
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .login-container {
        background: #fff;
        padding: 2rem;
        border-radius: 12px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        width: 100%;
        max-width: 400px;
    }

    .login-container h2 {
        text-align: center;
        margin-bottom: 1.5rem;
        font-weight: bold;
        color: #343a40;
    }

    .btn-primary {
        background-color: #007bff;
        border: none;
    }

    .btn-primary:hover {
        background-color: #0056b3;
    }

    .small-text {
        font-size: 0.9rem;
        color: #6c757d;
        text-align: center;
        margin-top: 1rem;
    }
    </style>
</head>

<body>

    <div class="login-container">
        <h2>Admin Login</h2>
        <form action="admin_login_process.php" method="POST">
            <div class="mb-3">
                <label for="adminEmail" class="form-label">Email address</label>
                <input type="email" class="form-control" id="adminEmail" name="email" placeholder="admin@example.com"
                    required>
            </div>
            <div class="mb-3">
                <label for="adminPassword" class="form-label">Password</label>
                <input type="password" class="form-control" id="adminPassword" name="password"
                    placeholder="Enter password" required>
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="rememberAdmin">
                <label class="form-check-label" for="rememberAdmin">Remember Me</label>
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>

        </form>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>