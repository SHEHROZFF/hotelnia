<?php

class EmailTemplate
{
    public static function getVerificationTemplate($verificationLink)
    {
        return "
            <html>
            <head>
                <style>
                    .email-container {
                        font-family: Arial, sans-serif;
                        max-width: 600px;
                        margin: 0 auto;
                        padding: 20px;
                        border: 1px solid #ddd;
                        border-radius: 8px;
                    }
                    .header {
                        background-color: #4CAF50;
                        padding: 10px;
                        text-align: center;
                        color: white;
                        border-radius: 8px 8px 0 0;
                    }
                    .body {
                        margin: 20px 0;
                    }
                    .footer {
                        text-align: center;
                        font-size: 12px;
                        color: #888;
                    }
                    .button {
                        display: inline-block;
                        padding: 10px 20px;
                        color: #fff;
                        background-color: #4CAF50;
                        text-decoration: none;
                        border-radius: 5px;
                        margin-top: 10px;
                    }
                </style>
            </head>
            <body>
                <div class='email-container'>
                    <div class='header'>
                        <h2>Email Verification</h2>
                    </div>
                    <div class='body'>
                        <p>Dear user,</p>
                        <p>Please click the link below to verify your email address:</p>
                        <a href='$verificationLink' class='button'>Verify Email</a>
                    </div>
                    <div class='footer'>
                        <p>If you did not request this email, please ignore it.</p>
                    </div>
                </div>
            </body>
            </html>
        ";
    }
    public function getResetpasswordTemplate($resetLink) {
        return '
        <html>
            <head>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        color: #333;
                        background-color: #f7f7f7;
                        margin: 0;
                        padding: 0;
                    }
                    .container {
                        width: 100%;
                        max-width: 600px;
                        margin: 20px auto;
                        background: #fff;
                        padding: 20px;
                        border-radius: 5px;
                        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                    }
                    h2 {
                        color: #4CAF50;
                    }
                    p {
                        font-size: 16px;
                        line-height: 1.6;
                    }
                    a.button {
                        display: inline-block;
                        padding: 10px 20px;
                        margin-top: 20px;
                        background-color: #4CAF50;
                        color: #fff;
                        text-decoration: none;
                        border-radius: 4px;
                    }
                    a.button:hover {
                        background-color: #45a049;
                    }
                </style>
            </head>
            <body>
                <div class="container">
                    <h2>Password Reset Request</h2>
                    <p>We received a request to reset the password for your account. Click the link below to set a new password:</p>
                    <p><a href="' . htmlspecialchars($resetLink) . '" class="button">Reset Password</a></p>
                    <p>If you did not request a password reset, please ignore this email.</p>
                    <p>Best regards,<br>Fennec</p>
                </div>
            </body>
        </html>';
    }
}


?>