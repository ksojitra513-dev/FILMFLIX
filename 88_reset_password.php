<?php
@session_start();
include_once '11_Config.php';

$title = "Reset Password - FLIMFLIX";

// 1. Security Check: Direct access block
if (!isset($_SESSION['forgot_email']) || !isset($_SESSION['forgot_otp_verified'])) {
    setcookie('error', 'Unauthorized access. Please verify OTP first.', time() + 5, '/');
    header("Location: 88_forgot_password.php");
    exit();
}

if (isset($_POST['reset_pwd_btn'])) {
    $email = mysqli_real_escape_string($con, $_SESSION['forgot_email']);
    $new_password = mysqli_real_escape_string($con, $_POST['new_password']);
    $confirm_password = mysqli_real_escape_string($con, $_POST['confirm_password']);

    // Server-side validation
    if (strlen($new_password) < 4) {
        $error_msg = "Password must be at least 4 characters.";
    } elseif ($new_password !== $confirm_password) {
        $error_msg = "Passwords do not match!";
    } else {
        // 2. Registration table ma password update karo
        $update_sql = "UPDATE user SET password = '$new_password' WHERE email = '$email'";

        if (mysqli_query($con, $update_sql)) {
            // --- MAM NO NIYAM: CLEANUP ---
            // 3. Token delete karo
            mysqli_query($con, "DELETE FROM password_token WHERE email = '$email'");

            // 4. Session cleanup
            unset($_SESSION['forgot_email']);
            unset($_SESSION['forgot_otp_verified']);

            setcookie('success', 'Password reset successfully! Login now.', time() + 5, '/');
            header("Location: 88_login.php");
            exit();
        } else {
            $error_msg = "Database Error: Could not update password.";
        }
    }
}

ob_start();
?>

<style>
    .reset-shell {
        min-height: 80vh;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
    }

    .reset-card {
        background: rgba(0, 0, 0, 0.75);
        padding: 60px;
        border-radius: 8px;
        width: 100%;
        max-width: 450px;
    }

    .reset-title {
        font-weight: 700;
        font-size: 32px;
        margin-bottom: 28px;
    }

    .form-control {
        background: #333 !important;
        border: none !important;
        color: white !important;
        padding: 12px !important;
        height: 50px;
    }

    .form-control:focus {
        background: #454545 !important;
        box-shadow: none;
        border-bottom: 2px solid #e87c03 !important;
    }

    .btn-netflix {
        background: #E50914;
        color: white;
        border: none;
        padding: 15px;
        width: 100%;
        font-weight: 700;
        font-size: 16px;
        border-radius: 4px;
        margin-top: 20px;
        transition: 0.3s;
    }

    .btn-netflix:hover {
        background: #b20710;
    }

    .btn-netflix:disabled {
        background: #555;
        cursor: not-allowed;
    }

    .error-box {
        background: #e87c03;
        color: white;
        padding: 10px 15px;
        border-radius: 4px;
        margin-bottom: 20px;
        font-size: 14px;
    }

    label {
        font-size: 14px;
        color: #8c8c8c;
        margin-bottom: 8px;
    }
</style>

<div class="reset-shell">
    <div class="reset-card shadow-lg">
        <h2 class="reset-title">Change Password</h2>

        <?php if (isset($error_msg)): ?>
            <div class='error-box'><i class="fas fa-exclamation-triangle me-2"></i> <?= $error_msg ?></div>
        <?php endif; ?>

        <form method="post" id="resetForm">
            <div class="mb-4">
                <label>New Password</label>
                <input type="password" class="form-control" name="new_password" id="n_pass" placeholder="Enter new password" required>
            </div>

            <div class="mb-4">
                <label>Confirm Password</label>
                <input type="password" class="form-control" name="confirm_password" id="c_pass" placeholder="Confirm new password" required>
                <div id="match_msg" class="mt-2 small fw-bold"></div>
            </div>

            <button type="submit" name="reset_pwd_btn" id="submit_btn" class="btn-netflix">Save Password</button>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('#c_pass, #n_pass').on('keyup', function() {
            var p = $('#n_pass').val();
            var cp = $('#c_pass').val();

            if (cp.length === 0) {
                $('#match_msg').hide();
                return;
            }

            if (p !== cp) {
                $('#match_msg').text('Passwords do not match').css('color', '#e87c03').show();
                $('#submit_btn').prop('disabled', true);
            } else {
                $('#match_msg').text('Passwords match ✓').css('color', '#2ecc71').show();
                $('#submit_btn').prop('disabled', false);
            }
        });
    });
</script>

<?php
$content = ob_get_clean();
include '88_layout.php';
?>