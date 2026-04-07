<?php
session_start();
require_once 'config.php';

// If already logged in, redirect to index
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = mysqli_real_escape_string($con, $_POST['fullname']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $number = mysqli_real_escape_string($con, $_POST['number']);
    $birthdate = mysqli_real_escape_string($con, $_POST['birthdate']);
    $city = mysqli_real_escape_string($con, $_POST['city']);
    $password = $_POST['password']; 
    
    // Check if email already exists
    $check = mysqli_query($con, "SELECT id FROM user WHERE email = '$email'");
    if (mysqli_num_rows($check) > 0) {
        $error = "Email already registered.";
    } else {
        $query = "INSERT INTO user (fullname, email, number, birthdate, city, password, role, status) 
                  VALUES ('$fullname', '$email', '$number', '$birthdate', '$city', '$password', 'user', 'active')";
        if (mysqli_query($con, $query)) {
            $success = "Registration successful! You can now login.";
        } else {
            $error = "Registration failed: " . mysqli_error($con);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FilmFlix • Registeration</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --bg-primary: #050508;
            --bg-card: rgba(255, 255, 255, 0.03);
            --border: rgba(255, 255, 255, 0.08);
            --accent-purple: #818cf8;
            --accent-cyan: #22d3ee;
            --text-primary: #ffffff;
            --text-muted: #94a3b8;
            --error: #f43f5e;
            --success: #10b981;
            --radius: 24px;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg-primary);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-x: hidden;
            padding: 40px 20px;
        }

        .bg-canvas {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            z-index: -1;
            background: radial-gradient(circle at 10% 20%, rgba(129, 140, 248, 0.1) 0%, transparent 40%),
                radial-gradient(circle at 90% 80%, rgba(34, 211, 238, 0.1) 0%, transparent 40%);
        }

        .blob {
            position: absolute;
            width: 600px; height: 600px;
            background: linear-gradient(135deg, var(--accent-purple), var(--accent-cyan));
            filter: blur(120px);
            border-radius: 50%;
            opacity: 0.15;
            animation: move 25s infinite alternate ease-in-out;
        }

        .blob-1 { top: -200px; left: -200px; }
        .blob-2 { bottom: -200px; right: -200px; animation-delay: -12s; }

        @keyframes move {
            from { transform: translate(0, 0) rotate(0deg) scale(1); }
            to { transform: translate(150px, 100px) rotate(30deg) scale(1.2); }
        }

        .auth-card {
            width: 100%; max-width: 600px;
            background: var(--bg-card);
            border: 1px solid var(--border);
            backdrop-filter: blur(30px);
            -webkit-backdrop-filter: blur(30px);
            padding: 48px;
            border-radius: var(--radius);
            box-shadow: 0 40px 100px rgba(0, 0, 0, 0.7);
            position: relative;
            z-index: 10;
        }

        .auth-header { text-align: center; margin-bottom: 40px; }
        
        .logo-ring {
            width: 72px; height: 72px;
            background: linear-gradient(135deg, var(--accent-purple), var(--accent-cyan));
            border-radius: 20px;
            display: flex; align-items: center; justify-content: center;
            font-size: 28px; color: #fff;
            margin: 0 auto 24px;
            box-shadow: 0 15px 30px rgba(129, 140, 248, 0.3);
            position: relative;
        }

        .auth-title {
            font-size: 28px; font-weight: 800; letter-spacing: -0.5px;
            margin-bottom: 8px;
            background: linear-gradient(to bottom, #fff, #94a3b8);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        }

        .auth-subtitle { font-size: 15px; color: var(--text-muted); font-weight: 400; }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .form-group.full { grid-column: span 2; }

        .form-label {
            display: block; font-size: 13px; font-weight: 600;
            color: var(--text-muted); margin-bottom: 10px;
            text-transform: uppercase; letter-spacing: 1.5px;
        }

        .input-container { position: relative; display: flex; align-items: center; }

        .input-icon {
            position: absolute; left: 18px; color: var(--text-muted);
            font-size: 16px; pointer-events: none; transition: 0.3s;
        }

        .form-control {
            width: 100%;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 16px 18px 16px 52px;
            color: var(--text-primary);
            font-family: inherit; font-size: 16px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            outline: none;
        }

        .form-control:focus {
            background: rgba(255, 255, 255, 0.07);
            border-color: var(--accent-purple);
            box-shadow: 0 0 0 4px rgba(129, 140, 248, 0.15);
        }

        .form-control:focus + .input-icon { color: var(--accent-purple); }
        
        input[type="date"]::-webkit-calendar-picker-indicator {
            filter: invert(1);
            opacity: 0.5;
            cursor: pointer;
        }

        .toggle-btn {
            position: absolute; right: 18px; background: none; border: none;
            color: var(--text-muted); cursor: pointer; padding: 4px; transition: 0.2s;
        }
        .toggle-btn:hover { color: #fff; }

        .status-box {
            padding: 14px 18px; border-radius: 14px; font-size: 14px; font-weight: 500;
            margin-bottom: 24px; display: flex; align-items: center; gap: 12px;
            animation: slideIn 0.4s cubic-bezier(0.18, 0.89, 0.32, 1.28);
        }

        .error-box { background: rgba(244, 63, 94, 0.1); border: 1px solid rgba(244, 63, 94, 0.2); color: var(--error); }
        .success-box { background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2); color: var(--success); }

        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .btn-submit {
            width: 100%; margin-top: 30px;
            background: linear-gradient(135deg, var(--accent-purple), var(--accent-cyan));
            color: #fff; border: none; padding: 18px; border-radius: 16px;
            font-family: inherit; font-size: 16px; font-weight: 700; cursor: pointer;
            transition: all 0.4s; display: flex; align-items: center; justify-content: center; gap: 12px;
            box-shadow: 0 10px 25px rgba(129, 140, 248, 0.25);
        }

        .btn-submit:hover {
            transform: translateY(-3px); box-shadow: 0 15px 35px rgba(129, 140, 248, 0.4); filter: brightness(1.1);
        }

        .auth-footer { margin-top: 30px; text-align: center; font-size: 14px; color: var(--text-muted); }
        .auth-footer a { color: var(--accent-cyan); text-decoration: none; font-weight: 600; transition: 0.3s; }
        .auth-footer a:hover { color: var(--accent-purple); text-decoration: underline; }

        @media (max-width: 600px) {
            .form-grid { grid-template-columns: 1fr; gap: 16px; }
            .form-group.full { grid-column: span 1; }
            .auth-card { padding: 32px 24px; }
        }
    </style>
</head>
<body>
    <div class="bg-canvas">
        <div class="blob blob-1"></div><div class="blob blob-2"></div>
    </div>

    <div class="auth-card">
        <div class="auth-header">
            <div class="logo-ring"><i class="fas fa-ticket-alt"></i></div>
            <h1 class="auth-title">Join FilmFlix</h1>
            <p class="auth-subtitle">Create an account to book your favorite movies</p>
        </div>

        <?php if ($error): ?>
            <div class="status-box error-box"><i class="fas fa-exclamation-circle"></i><span><?= $error ?></span></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="status-box success-box"><i class="fas fa-check-circle"></i><span><?= $success ?></span></div>
        <?php endif; ?>

        <form id="authForm" method="POST" autocomplete="off">
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label" for="fullname">Full Name</label>
                    <div class="input-container">
                        <i class="fas fa-user input-icon"></i>
                        <input type="text" id="fullname" name="fullname" class="form-control" placeholder="John Doe" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">Email Address</label>
                    <div class="input-container">
                        <i class="fas fa-envelope input-icon"></i>
                        <input type="email" id="email" name="email" class="form-control" placeholder="john@email.com" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="number">Phone Number</label>
                    <div class="input-container">
                        <i class="fas fa-phone input-icon"></i>
                        <input type="text" id="number" name="number" class="form-control" placeholder="10-digit number" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="city">City</label>
                    <div class="input-container">
                        <i class="fas fa-map-marker-alt input-icon"></i>
                        <input type="text" id="city" name="city" class="form-control" placeholder="Your City" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="birthdate">Date of Birth</label>
                    <div class="input-container">
                        <i class="fas fa-calendar input-icon"></i>
                        <input type="date" id="birthdate" name="birthdate" class="form-control" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <div class="input-container">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required>
                        <button type="button" class="toggle-btn" id="eye"><i class="fas fa-eye"></i></button>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn-submit" id="submitBtn">
                <span>Create Account</span>
                <i class="fas fa-arrow-right"></i>
            </button>
        </form>

        <div class="auth-footer">
            <p>Already have an account? <a href="login.php">Log In Here</a></p>
        </div>
    </div>

    <script>
        const passwordField = document.getElementById('password');
        const eyeBtn = document.getElementById('eye');

        eyeBtn.addEventListener('click', () => {
            const isPass = passwordField.type === 'password';
            passwordField.type = isPass ? 'text' : 'password';
            eyeBtn.innerHTML = isPass ? '<i class="fas fa-eye-slash"></i>' : '<i class="fas fa-eye"></i>';
        });

        const authForm = document.getElementById('authForm');
        const submitBtn = document.getElementById('submitBtn');

        authForm.addEventListener('submit', () => {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span>Processing...</span><i class="fas fa-spinner fa-spin"></i>';
            submitBtn.style.opacity = '0.7';
        });
    </script>
</body>
</html>
