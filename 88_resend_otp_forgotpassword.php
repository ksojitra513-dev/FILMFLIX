<?php
ob_start();
session_start();
include_once '11_Config.php';
include '88_mailer.php';
include '88_mail_content.php';

// Check karo ke session ma email chhe ke nahi
if (isset($_SESSION['forgot_email'])) {
    $email = mysqli_real_escape_string($con, $_SESSION['forgot_email']);

    // 1. User nu naam fetch karo
    $user_query = mysqli_query($con, "SELECT fullname FROM user WHERE email = '$email' LIMIT 1");
    $user_data = mysqli_fetch_assoc($user_query);

    if (!$user_data) {
        header("Location: 88_forgot_password.php");
        exit();
    }

    // 2. Mam no Niyam: Check Resend Limit & Time Block
    $token_query = mysqli_query($con, "SELECT resend_count, last_resend FROM password_token WHERE email = '$email' LIMIT 1");
    $row = mysqli_fetch_assoc($token_query);

    if ($row) {
        $time_diff = time() - strtotime($row['last_resend']);

        // Jo 3 attempts thai gaya hoy ane 5 min (300 sec) na thaya hoy to block
        if ($row['resend_count'] >= 3 && $time_diff < 300) {
            $wait = ceil((300 - $time_diff) / 60);
            setcookie('error', "Too many requests. Please wait $wait minutes.", time() + 5, "/");
            header("Location: 88_verify_otp.php");
            exit();
        }

        // Navo OTP ane 2-minute Expiry set karo
        $new_otp = rand(100000, 999999);
        $expires_at = date("Y-m-d H:i:s", strtotime('+2 minutes')); // Rule: 2 Min Expiry
        $new_count = $row['resend_count'] + 1;
        // $new_otp = rand(100000, 999999);
        // $current_time_str = date("Y-m-d H:i:s");
        // $expires_at = date("Y-m-d H:i:s", strtotime($current_time_str . " +2 minutes"));
        // Database Update: Navo OTP, Navi Expiry ane Count vadharo
        $update = mysqli_query($con, "UPDATE password_token SET 
            otp = '$new_otp', 
            expires_at = '$expires_at', 
            resend_count = '$new_count', 
            last_resend = NOW() 
            WHERE email = '$email'");

        // $update = mysqli_query($con, "UPDATE password_token SET 
        //     otp = '$new_otp', 
        //     expires_at = '$expires_at', 
        //     resend_count = '$new_count', 
        //     last_resend = '$current_time_str' 
        //     WHERE email = '$email'");


        if ($update) {
            // Email moklo
            $subject = "FLIMFLIX - Your New OTP";
            $body = getForgotPasswordOtpEmailBody($new_otp, $user_data['fullname']);

            if (sendEmail($email, $subject, $body)) {
                setcookie("success", "A fresh OTP has been sent. Valid for 2 mins.", time() + 5, "/");
                header("Location: 88_verify_otp.php");
            } else {
                setcookie("error", "Mail delivery failed. Try again.", time() + 5, "/");
                header("Location: 88_verify_otp.php");
            }
        }
    } else {
        // Jo record j na hoy to direct forgot password par
        header("Location: 88_forgot_password.php");
    }
} else {
    header("Location: 88_login.php");
}
exit();
