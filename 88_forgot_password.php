<?php
ob_start();
session_start();

// 1. Timezone set karvu sauthi mhatvanu chhe
date_default_timezone_set('Asia/Kolkata');

include_once '11_Config.php';
include '88_mailer.php';
include '88_mail_content.php';

$error_msg = "";

if (isset($_POST['send_otp'])) {
    $email = mysqli_real_escape_string($con, trim($_POST['email']));

    // 2. Database mathi juni details check karo
    $check_resend = mysqli_query($con, "SELECT last_resend, resend_count FROM password_token WHERE email='$email' LIMIT 1");
    $row = mysqli_fetch_assoc($check_resend);

    $current_resend_count = 0;

    if ($row) {
        $last_resend_time = strtotime($row['last_resend']);
        $current_time = time();
        $time_diff = $current_time - $last_resend_time;

        // logic: Jo 5 minute (300 sec) pati gaya hoy, to count reset kari do
        if ($time_diff >= 300) {
            $current_resend_count = 0;
        } else {
            $current_resend_count = $row['resend_count'];
        }

        // Rule: Jo 3 var OTP mangavi lidho hoy ane haji 5 min na thai hoy to BLOCK
        if ($current_resend_count >= 3 && $time_diff < 300) {
            $wait_seconds = 300 - $time_diff;
            $wait_minutes = ceil($wait_seconds / 60);
            $error_msg = "Too many attempts. Blocked for $wait_minutes more minutes.";
        }
    }

    if (empty($error_msg)) {
        $otp = rand(100000, 999999);
        // Rule: OTP valid for 2 min only (31 min vali error fix)
        $expires_at = date("Y-m-d H:i:s", time() + 120);
        // --- જૂનો કોડ શોધો અને આનાથી બદલો ---
        // $otp = rand(100000, 999999);
        // $current_time_str = date("Y-m-d H:i:s"); // અત્યારનો સમય string માં
        // $expires_at = date("Y-m-d H:i:s", strtotime($current_time_str . " +2 minutes"));
        // 3. Juni entry delete karo
        mysqli_query($con, "DELETE FROM password_token WHERE email='$email'");

        // Navo count set karo
        $new_resend_count = $current_resend_count + 1;

        // last_resend ma NOW() nakhavu jaruri chhe jethi block logic chale
        $insert = mysqli_query($con, "INSERT INTO password_token (email, otp, expires_at, resend_count, last_resend) 
                                      VALUES ('$email', '$otp', '$expires_at', '$new_resend_count', NOW())");
        // $insert = mysqli_query($con, "INSERT INTO password_token (email, otp, expires_at, resend_count, last_resend) 
        //                       VALUES ('$email', '$otp', '$expires_at', '$new_resend_count', '$current_time_str')");

        if ($insert) {
            // User nu naam fetch karo email mate
            $u_res = mysqli_query($con, "SELECT fullname FROM user WHERE email='$email'");
            $u_data = mysqli_fetch_assoc($u_res);
            $name = ($u_data) ? $u_data['fullname'] : "User";

            if (sendEmail($email, "FLIMFLIX OTP", getForgotPasswordOtpEmailBody($otp, $name))) {
                $_SESSION['forgot_email'] = $email;
                header("Location: 88_verify_otp.php");
                exit();
            } else {
                $error_msg = "Mail server error. Please try again later.";
            }
        }
    }
}
?>

<style>
    .forgot-shell {
        min-height: 80vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #141414;
        color: white;
    }

    .forgot-wrap {
        background: rgba(0, 0, 0, 0.9);
        padding: 40px;
        border-radius: 10px;
        width: 100%;
        max-width: 400px;
        border: 1px solid #333;
    }

    .form-control {
        background: #646363 !important;
        color: white !important;
        border: 1px solid #444 !important;
        margin-bottom: 10px;
        padding: 12px;
        width: 100%;
        border-radius: 5px;
    }

    .btn-netflix {
        background: #E50914;
        color: white;
        width: 100%;
        padding: 12px;
        border: none;
        border-radius: 5px;
        font-weight: bold;
        cursor: pointer;
    }

    .btn-netflix:disabled {
        background: #555;
        cursor: not-allowed;
        opacity: 0.6;
    }

    .error-box {
        background: rgba(229, 9, 20, 0.2);
        border-left: 4px solid #E50914;
        color: #ff9999;
        padding: 10px;
        margin-bottom: 20px;
        font-size: 13px;
        border-radius: 4px;
    }
</style>

<div class="forgot-shell">
    <div class="forgot-wrap">
        <h2 style="color: #E50914; margin-bottom: 20px;">Forgot Password</h2>

        <?php if ($error_msg): ?>
            <div class="error-box"><?php echo $error_msg; ?></div>
        <?php endif; ?>

        <form method="POST" action="88_forgot_password.php">
            <label style="color: #f0e7e7; font-size: 14px;">Registered Email Address</label>
            <input type="email" id="email_box" name="email" class="form-control" placeholder="example@mail.com" autocomplete="off" required>
            <span id="ajax_msg" style="font-size:12px; display:block; margin-bottom:15px; min-height: 15px;"></span>

            <button type="submit" name="send_otp" id="send_btn" class="btn-netflix" disabled>Send OTP</button>
        </form>
    </div>
</div>

<script>
    document.getElementById('email_box').addEventListener('input', function() {
        let email = this.value;
        let msg = document.getElementById('ajax_msg');
        let btn = document.getElementById('send_btn');

        if (email.length > 5) {
            fetch('88_check_email_ajax.php?email=' + encodeURIComponent(email))
                .then(res => res.text())
                .then(data => {
                    if (data.trim() === 'exists') {
                        msg.innerText = "Verified Account ✅";
                        msg.style.color = "#00df81";
                        // Jo error message display ma hoy (blocked), to button disable j rakhvu
                        <?php if ($error_msg == ""): ?>
                            btn.disabled = false;
                        <?php else: ?>
                            btn.disabled = true;
                        <?php endif; ?>
                    } else {
                        msg.innerText = "Email not found in our records ❌";
                        msg.style.color = "#E50914";
                        btn.disabled = true;
                    }
                });
        } else {
            msg.innerText = "";
            btn.disabled = true;
        }
    });
</script>

<?php
$content = ob_get_clean();
include '88_layout.php';
?>