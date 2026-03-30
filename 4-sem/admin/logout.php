<?php
session_start();

// Handle the actual secure logout action
if (isset($_POST['logout_confirm'])) {
    session_destroy();
    // Redirect to the main frontend website
    header("Location: ../index.php");
    exit;
}

// If user decides to stay
if (isset($_POST['stay'])) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>FilmFlix Admin — Secure Logout</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
  :root {
    --bg-primary: #0a0a0f;
    --bg-card: rgba(255, 255, 255, 0.04);
    --border: rgba(255, 255, 255, 0.08);
    --text-primary: #f1f1f5;
    --text-muted: #9494a8;
    --accent: #ef4444; 
    --radius: 16px;
  }
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body {
    font-family: 'Inter', system-ui, sans-serif;
    background: var(--bg-primary);
    color: var(--text-primary);
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background-image: radial-gradient(circle at center, rgba(239, 68, 68, 0.05) 0%, transparent 60%);
  }
  .logout-card {
    background: var(--bg-card);
    backdrop-filter: blur(20px);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 40px;
    width: 100%;
    max-width: 420px;
    text-align: center;
    box-shadow: 0 24px 60px rgba(0,0,0,0.4);
    animation: scaleIn 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
  }
  @keyframes scaleIn {
    0% { transform: scale(0.9); opacity: 0; }
    100% { transform: scale(1); opacity: 1; }
  }
  
  .icon-wrapper {
    width: 80px;
    height: 80px;
    background: rgba(239, 68, 68, 0.1);
    border: 2px solid rgba(239, 68, 68, 0.2);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    color: var(--accent);
    font-size: 32px;
    margin: 0 auto 24px;
    position: relative;
  }
  
  .icon-wrapper::after {
    content: '';
    position: absolute; inset: -10px;
    border: 1px dashed rgba(239, 68, 68, 0.3);
    border-radius: 50%;
    animation: rotate 10s linear infinite;
  }
  @keyframes rotate { 100% { transform: rotate(360deg); } }
  
  h1 { font-size: 22px; font-weight: 700; margin-bottom: 12px; }
  p { font-size: 14px; color: var(--text-muted); line-height: 1.5; margin-bottom: 32px; }
  
  .btn-group {
    display: flex; gap: 12px; flex-direction: column;
  }
  
  .btn {
    display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    padding: 14px 24px; border-radius: 8px;
    font-family: inherit; font-size: 14px; font-weight: 600;
    cursor: pointer; border: none; transition: all 0.2s;
  }
  
  .btn-danger {
    background: var(--accent); color: #fff;
    box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);
  }
  .btn-danger:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(239, 68, 68, 0.4); }
  
  .btn-secondary {
    background: transparent; border: 1px solid var(--border); color: var(--text-primary);
  }
  .btn-secondary:hover { background: rgba(255,255,255,0.05); }
  
</style>
</head>
<body>

<div class="logout-card">
  <div class="icon-wrapper">
    <i class="fas fa-sign-out-alt"></i>
  </div>
  
  <h1>Leaving so soon?</h1>
  <p>You are about to sign out of the FilmFlix administrative panel. Any unsaved changes may be lost.</p>
  
  <form method="POST" class="btn-group">
    <button type="submit" name="logout_confirm" class="btn btn-danger">
      <i class="fas fa-power-off"></i> Yes, Log me out
    </button>
    <button type="submit" name="stay" class="btn btn-secondary">
      Wait, take me back
    </button>
  </form>
</div>

</body>
</html>
