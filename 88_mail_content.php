<?php
// ============================================================
// mail_content.php — FLIMFLIX Movie Website
// ============================================================

// ---- Shared inline CSS for FLIMFLIX Movie Theme ----
function _emailStyles()
{
    return '
        body { font-family: "Helvetica", Arial, sans-serif; background:#141414; margin:0; padding:0; color: #ffffff; }
        .email-wrapper { max-width:600px; margin:20px auto; background:#000000; border-radius:8px; overflow:hidden; border: 1px solid #333; }
        .header { background:#E50914; padding:25px; text-align:center; color:#ffffff; font-size:28px; font-weight:bold; text-transform: uppercase; letter-spacing: 2px; }
        .content { padding:30px; background:#141414; color:#ffffff; line-height: 1.6; }
        .otp-box { margin:25px auto; padding:20px; width:200px; text-align:center; background:#1f1f1f; border: 2px dashed #E50914; border-radius:12px; color:#E50914; font-size:35px; font-weight:bold; letter-spacing:8px; }
        .footer { padding:20px; text-align:center; font-size:12px; color:#666; background:#000; border-top: 1px solid #333; }
        .btn-link { color: #E50914; text-decoration: none; font-weight: bold; }
    ';
}

// ---- Standard Wrapper ----
function _wrapEmail($headerTitle, $bodyContent)
{
    $styles = _emailStyles();
    return "
    <html>
    <head><style>{$styles}</style></head>
    <body>
        <div class='email-wrapper'>
            <div class='header'>FLIMFLIX</div>
            <div class='content'>
                <h2 style='color:#E50914; text-align:center; margin-top:0;'>{$headerTitle}</h2>
                {$bodyContent}
            </div>
            <div class='footer'>&copy; " . date('Y') . " FLIMFLIX Movies. Enjoy your favorite shows!</div>
        </div>
    </body>
    </html>";
}

// 1. OTP Email Template (Updated for 2-minute Rule)
function getForgotPasswordOtpEmailBody($otp, $name = 'User')
{
    $safeName = htmlspecialchars($name ?: 'User');
    $styles = _emailStyles();

    return "
    <html>
    <head><style>{$styles}</style></head>
    <body>
        <div class='email-wrapper'>
            <div class='header'>FLIMFLIX</div>
            <div class='content'>
                <p>Hello <strong>{$safeName}</strong>,</p>
                <p>We received a request to reset your password. Use the following Secure OTP to proceed:</p>
                
                <div class='otp-box'>{$otp}</div>
                
                <p style='text-align:center; font-size:14px; color:#ff9999;'><strong>Warning:</strong> This OTP is valid for 2 minutes only.</p>
                <p>If the OTP expires, you will need to request a new one from the website.</p>
                <p>If you didn't request this, you can safely ignore this email.</p>
            </div>
            <div class='footer'>This is an automated security mail from Flimflix. Please do not reply.</div>
        </div>
    </body>
    </html>";
}

// 2. Welcome Email
function getWelcomeEmailBody($name, $email)
{
    $body = "
        <p>Welcome to the family, <strong>" . htmlspecialchars($name) . "</strong>!</p>
        <p>Your FLIMFLIX account is now active. Get ready for unlimited movies, TV shows, and more.</p>
        <p><strong>Login Email:</strong> " . htmlspecialchars($email) . "</p>
        <div style='text-align:center; margin-top:30px;'>
            <a href='#' style='background:#E50914; color:#ffffff; padding:15px 30px; text-decoration:none; border-radius:4px; font-weight:bold; display:inline-block;'>Start Watching Now</a>
        </div>
    ";
    return _wrapEmail('Welcome to FLIMFLIX! 🍿', $body);
}

// 3. Subscription Confirmation
function getOrderConfirmationBody($order, $items)
{
    $body = "
        <p>Hello, <strong>" . htmlspecialchars($order['delivery_name']) . "</strong>!</p>
        <p>Your subscription/order <strong>#" . htmlspecialchars($order['order_number']) . "</strong> has been confirmed.</p>
        <p><strong>Total Amount:</strong> ₹" . number_format($order['total_amount'], 2) . "</p>
        <p>Enjoy your premium access to FLIMFLIX movies and originals!</p>
    ";
    return _wrapEmail('Order Confirmed!', $body);
}
