<?php
session_start();

// Check user auth
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FilmFlix • Home</title>
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
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg-primary);
            color: var(--text-primary);
            margin: 0; padding: 20px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: var(--bg-card);
            border: 1px solid var(--border);
            padding: 20px 40px;
            border-radius: 20px;
            backdrop-filter: blur(10px);
        }

        .logo { font-size: 24px; font-weight: 800; display: flex; align-items: center; gap: 10px; }
        .logo i { background: linear-gradient(135deg, var(--accent-purple), var(--accent-cyan)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }

        .user-info { display: flex; align-items: center; gap: 20px; }
        .welcome { color: var(--text-muted); }
        .welcome strong { color: #fff; }

        .btn-logout {
            background: rgba(244, 63, 94, 0.1);
            color: #f43f5e;
            border: 1px solid rgba(244, 63, 94, 0.2);
            padding: 10px 20px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            transition: 0.3s;
            display: inline-flex; align-items: center; gap: 8px;
        }

        .btn-logout:hover {
            background: #f43f5e;
            color: #fff;
        }
        
        .main-content {
            text-align: center;
            margin-top: 100px;
        }
        
        .main-title {
            font-size: 48px;
            font-weight: 800;
            margin-bottom: 20px;
            background: linear-gradient(to right, var(--accent-purple), var(--accent-cyan));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="logo"><i class="fas fa-film"></i> FilmFlix</div>
        <div class="user-info">
            <span class="welcome">Welcome back, <strong><?= htmlspecialchars($_SESSION['user_name']) ?></strong>!</span>
            <a href="logout.php" class="btn-logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </header>
    
    <div class="main-content">
        <h1 class="main-title">Enjoy The Cinematic Experience</h1>
        <p style="color: var(--text-muted); font-size: 18px; max-width: 600px; margin: 0 auto;">You have successfully logged into your account. The full user interface will be developed here soon!</p>
    </div>
</body>
</html>
