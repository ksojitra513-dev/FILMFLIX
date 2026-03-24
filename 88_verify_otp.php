<?php
ob_start();
session_start();
include_once '11_Config.php';

$title = "Verify OTP - FLIMFLIX";

// Direct access check
if (!isset($_SESSION['forgot_email'])) {
    header("Location: 88_forgot_password.php");
    exit();
}

$email = $_SESSION['forgot_email'];
$error_msg = "";

// Fetch token data
$res = mysqli_query($con, "SELECT * FROM password_token WHERE email='$email' LIMIT 1");
$data = mysqli_fetch_assoc($res);

if (!$data) {
    setcookie('error', 'No active OTP request found.', time() + 5, '/');
    header("Location: 88_forgot_password.php");
    exit();
}

// Logic for Verification
if (isset($_POST['verify_otp_btn'])) {
    $user_otp = mysqli_real_escape_string($con, $_POST['otp']);

    // Check if Expired
    if (strtotime($data['expires_at']) < time()) {
        $error_msg = "OTP Expired! Please regenerate.";
    }
    // Match OTP
    elseif ($data['otp'] == $user_otp) {
        $_SESSION['forgot_otp_verified'] = true;
        header("Location: 88_reset_password.php");
        exit();
    }
    // Wrong OTP
    else {
        mysqli_query($con, "UPDATE password_token SET otp_attempts = otp_attempts + 1 WHERE email='$email'");
        $error_msg = "Invalid OTP! Please try again.";
    }
}

$expiry_timestamp = strtotime($data['expires_at']) * 1000;
?>

<style>
    .otp-shell {
        min-height: 80vh;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
    }

    .otp-card {
        background: rgba(0, 0, 0, 0.85);
        padding: 50px;
        border-radius: 8px;
        width: 100%;
        max-width: 450px;
        text-align: center;
        border: 1px solid #333;
    }

    .otp-digit {
        width: 45px;
        height: 55px;
        background: #333 !important;
        color: white !important;
        text-align: center;
        font-size: 24px;
        font-weight: bold;
        border: 1px solid #444;
        border-radius: 4px;
        margin: 0 5px;
    }

    .otp-digit:focus {
        border: 2px solid #E50914;
        outline: none;
    }

    .btn-netflix {
        background: #E50914;
        color: white;
        width: 100%;
        padding: 14px;
        border: none;
        border-radius: 4px;
        font-weight: 700;
        margin-top: 25px;
        cursor: pointer;
    }

    .btn-netflix:disabled {
        background: #555;
        cursor: not-allowed;
        opacity: 0.6;
    }

    .timer-text {
        font-size: 15px;
        margin-top: 25px;
        color: #8c8c8c;
    }

    #resend-link {
        color: #E50914;
        text-decoration: none;
        font-weight: bold;
        display: none;
        border: 1px solid #E50914;
        padding: 8px 15px;
        border-radius: 4px;
        transition: 0.3s;
    }

    #resend-link:hover {
        background: #E50914;
        color: white;
    }
</style>

<div class="otp-shell">
    <div class="otp-card shadow-lg">
        <h2 class="fw-bold mb-3" style="color: #E50914;">Verify OTP</h2>
        <p class="text-muted small mb-4">Enter the code sent to your email.</p>

        <?php if ($error_msg): ?>
            <div class="alert alert-danger py-2 small" style="background: rgba(229, 9, 20, 0.2); border: 1px solid #E50914; color: white;">
                <?= $error_msg ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST" id="otpForm">
            <div class="d-flex justify-content-center mb-4">
                <?php for ($i = 0; $i < 6; $i++): ?>
                    <input type="text" class="otp-digit" maxlength="1" inputmode="numeric" required <?= ($i == 0) ? 'autofocus' : '' ?>>
                <?php endfor; ?>
            </div>

            <input type="hidden" name="otp" id="otp_full">
            <button type="submit" name="verify_otp_btn" id="verify_btn" class="btn-netflix">Verify OTP</button>
        </form>

        <div class="timer-text">
            <span id="timer-container">Time remaining: <b id="timer" style="color: white;">02:00</b></span>
            <br><br>
            <a href="88_resend_otp_forgotpassword.php" id="resend-link">Regenerate OTP</a>
        </div>
    </div>
</div>

<script>
    // Timer Logic
    const expiryTime = <?= $expiry_timestamp ?>;
    const timerDisplay = document.getElementById('timer');
    const resendLink = document.getElementById('resend-link');
    const timerContainer = document.getElementById('timer-container');
    const verifyBtn = document.getElementById('verify_btn');

    function updateTimer() {
        const now = new Date().getTime();
        const diff = expiryTime - now;

        if (diff <= 0) {
            timerDisplay.innerHTML = "00:00";
            timerContainer.style.display = 'none'; // Timer chhupavi dyo
            resendLink.style.display = 'inline-block'; // Regenerate option batavo
            verifyBtn.disabled = true; // Button block karo
            return;
        }

        const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((diff % (1000 * 60)) / 1000);
        timerDisplay.innerHTML = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
    }

    setInterval(updateTimer, 1000);
    updateTimer();

    // OTP Auto-focus & Hidden Input Logic
    const inputs = document.querySelectorAll('.otp-digit');
    const hiddenInput = document.getElementById('otp_full');

    inputs.forEach((input, index) => {
        input.addEventListener('input', (e) => {
            if (e.target.value && index < inputs.length - 1) inputs[index + 1].focus();
            let fullOtp = "";
            inputs.forEach(i => fullOtp += i.value);
            hiddenInput.value = fullOtp;
        });
        input.addEventListener('keydown', (e) => {
            if (e.key === 'Backspace' && !input.value && index > 0) inputs[index - 1].focus();
        });
    });
</script>

<?php
$content = ob_get_clean();
include '88_layout.php';
?>