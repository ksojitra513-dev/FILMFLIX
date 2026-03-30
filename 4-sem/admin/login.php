<?php
session_start();
require_once '../config.php';

// If already logged in, redirect to dashboard
if (isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $password = $_POST['password'];

    if (!empty($email) && !empty($password)) {
        // Query to check admin user
        $query = "SELECT * FROM user WHERE email = '$email' AND role = 'admin' AND status = 'active' LIMIT 1";
        $result = mysqli_query($con, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            $admin = mysqli_fetch_assoc($result);
            
            // Check password (using plain text as per current database pattern)
            // Ideally should be password_verify($password, $admin['password'])
            if ($password === $admin['password']) {
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_name'] = $admin['fullname'];
                $_SESSION['admin_email'] = $admin['email'];
                $_SESSION['admin_avatar'] = $admin['image'];
                
                header("Location: index.php");
                exit();
            } else {
                $error = "Incorrect password. Please try again.";
            }
        } else {
            $error = "No administrator account found with that email.";
        }
    } else {
        $error = "Please fill in all fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FilmFlix Admin — Portal Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --bg-primary: #0a0a0f;
            --bg-card: rgba(255, 255, 255, 0.03);
            --border: rgba(255, 255, 255, 0.08);
            --accent: #a855f7;
            --accent-cyan: #06b6d4;
            --text-primary: #f1f1f5;
            --text-muted: #9494a8;
            --shadow: 0 24px 60px rgba(0, 0, 0, 0.6);
            --radius: 20px;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-primary);
            color: var(--text-primary);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            background-image: 
                radial-gradient(circle at 10% 20%, rgba(168, 85, 247, 0.15) 0%, transparent 40%),
                radial-gradient(circle at 90% 80%, rgba(6, 182, 212, 0.15) 0%, transparent 40%);
        }

        /* Animated Blobs */
        .blob {
            position: absolute;
            width: 500px;
            height: 500px;
            background: linear-gradient(135deg, rgba(168, 85, 247, 0.2), rgba(6, 182, 212, 0.2));
            filter: blur(80px);
            border-radius: 50%;
            z-index: -1;
            animation: move 20s infinite alternate;
        }
        .blob-1 { top: -100px; left: -100px; }
        .blob-2 { bottom: -100px; right: -100px; animation-delay: -10s; }

        @keyframes move {
            from { transform: translate(0, 0) scale(1); }
            to { transform: translate(100px, 50px) scale(1.1); }
        }

        .login-card {
            width: 100%;
            max-width: 420px;
            background: var(--bg-card);
            border: 1px solid var(--border);
            backdrop-filter: blur(40px);
            padding: 40px;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            animation: fadeIn 0.8s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .login-header {
            text-align: center;
            margin-bottom: 35px;
        }

        .logo-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--accent), var(--accent-cyan));
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: #fff;
            margin: 0 auto 20px;
            box-shadow: 0 10px 20px rgba(168, 85, 247, 0.3);
        }

        .login-title {
            font-size: 24px;
            font-weight: 800;
            background: linear-gradient(135deg, #fff, var(--text-muted));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 8px;
        }

        .login-subtitle {
            font-size: 14px;
            color: var(--text-muted);
            letter-spacing: 0.5px;
        }

        .form-group {
            margin-bottom: 22px;
            position: relative;
        }

        .form-label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-muted);
            margin-bottom: 8px;
            padding-left: 2px;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon {
            position: absolute;
            left: 15px;
            color: var(--text-muted);
            font-size: 14px;
            pointer-events: none;
            transition: 0.3s;
        }

        .form-control {
            width: 100%;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 14px 15px 14px 45px;
            color: var(--text-primary);
            font-family: inherit;
            font-size: 15px;
            transition: all 0.3s;
            outline: none;
        }

        .form-control:focus {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(168, 85, 247, 0.5);
            box-shadow: 0 0 0 4px rgba(168, 85, 247, 0.1);
        }

        .form-control:focus + .input-icon {
            color: var(--accent);
        }

        .toggle-password {
            position: absolute;
            right: 15px;
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            padding: 5px;
            display: flex;
            align-items: center;
            transition: 0.3s;
        }

        .toggle-password:hover { color: #fff; }

        .btn-login {
            width: 100%;
            background: linear-gradient(135deg, var(--accent), var(--accent-cyan));
            color: #fff;
            border: none;
            padding: 14px;
            border-radius: 12px;
            font-family: inherit;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            box-shadow: 0 4px 15px rgba(168, 85, 247, 0.25);
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(168, 85, 247, 0.35);
            opacity: 0.95;
        }

        .btn-login:active { transform: translateY(0); }

        .error-alert {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: #ef4444;
            padding: 12px;
            border-radius: 10px;
            font-size: 13.5px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: shake 0.5s cubic-bezier(.36,.07,.19,.97) both;
        }

        @keyframes shake {
            10%, 90% { transform: translate3d(-1px, 0, 0); }
            20%, 80% { transform: translate3d(2px, 0, 0); }
            30%, 50%, 70% { transform: translate3d(-4px, 0, 0); }
            40%, 60% { transform: translate3d(4px, 0, 0); }
        }

        .login-footer {
            margin-top: 30px;
            text-align: center;
            font-size: 13px;
            color: var(--text-muted);
        }

        .login-footer a {
            color: var(--accent-cyan);
            text-decoration: none;
            font-weight: 600;
        }

        .login-footer a:hover { text-decoration: underline; }

        /* Custom Scrollbar for form if needed */
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }

    </style>
</head>
<body>
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>

    <div class="login-card">
        <div class="login-header">
            <div class="logo-icon">
                <i class="fas fa-film"></i>
            </div>
            <h1 class="login-title">Admin Portal</h1>
            <p class="login-subtitle">Secured Entry for FilmFlix Staff</p>
        </div>

        <?php if ($error): ?>
            <div class="error-alert">
                <i class="fas fa-exclamation-circle"></i>
                <span><?= $error ?></span>
            </div>
        <?php endif; ?>

        <form id="loginForm" method="POST" autocomplete="off">
            <div class="form-group">
                <label class="form-label" for="email">Admin Email</label>
                <div class="input-wrapper">
                    <i class="fas fa-envelope input-icon"></i>
                    <input type="email" id="email" name="email" class="form-control" placeholder="staff@filmflix.com" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Security Password</label>
                <div class="input-wrapper">
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required>
                    <button type="button" class="toggle-password" id="passToggle">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn-login" id="loginBtn">
                <span>Access Dashboard</span>
                <i class="fas fa-arrow-right"></i>
            </button>
        </form>

        <div class="login-footer">
            <p>Back to <a href="../index.php">Main Site</a></p>
        </div>
    </div>

    <script>
        // Password visibility toggle
        const passInp = document.getElementById('password');
        const passTog = document.getElementById('passToggle');
        
        passTog.addEventListener('click', () => {
            const type = passInp.getAttribute('type') === 'password' ? 'text' : 'password';
            passInp.setAttribute('type', type);
            passTog.innerHTML = type === 'password' ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
        });

        // Form submission loading state
        const loginForm = document.getElementById('loginForm');
        const loginBtn = document.getElementById('loginBtn');

        loginForm.addEventListener('submit', () => {
            loginBtn.disabled = true;
            loginBtn.innerHTML = '<span>Verifying...</span><i class="fas fa-circle-notch fa-spin"></i>';
            loginBtn.style.opacity = '0.8';
        });

        // Focus animation enhancement
        const inputs = document.querySelectorAll('.form-control');
        inputs.forEach(input => {
            input.addEventListener('focus', () => {
                input.parentElement.style.transform = 'scale(1.02)';
                input.parentElement.style.transition = '0.3s';
            });
            input.addEventListener('blur', () => {
                input.parentElement.style.transform = 'scale(1)';
            });
        });
    </script>
</body>
</html>
