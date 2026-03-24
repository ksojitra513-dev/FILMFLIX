<?php 
session_start();
include 'header.php'; // Aapka header file
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - FILMFLIX</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { 
            background: #000 !important; 
            color: white; 
            height: 100vh; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            font-family: Arial, sans-serif;
        }
        .box { 
            background: #111; 
            padding: 35px; 
            border-radius: 12px; 
            border: 1px solid #222; 
            width: 100%; 
            max-width: 400px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.5); 
        }
        .btn-red { 
            background: #e50914 !important; 
            color: white !important; 
            border: none !important; 
            font-weight: bold; 
            transition: 0.3s; 
        }
        .btn-red:hover { 
            background: #b20710 !important; 
            transform: translateY(-2px); 
        }
        .form-control { 
            background: #222; 
            border: 1px solid #444; 
            color: white; 
            padding: 10px; 
        }
        .form-control:focus { 
            background: #2b2b2b; 
            color: white; 
            border-color: #e50914; 
            box-shadow: none; 
        }
        label { font-size: 0.85rem; color: #aaa; }
        label.error { color: #e50914; font-size: 0.8rem; margin-top: 5px; display: block; }
    </style>
</head>
<body>

<div class="box">
    <h2 class="text-danger fw-bold text-center mb-4">FILMFLIX</h2>
    <h5 class="text-center mb-4 text-white">Reset Your Password</h5>

    <?php if(isset($_COOKIE['success'])): ?>
        <div class="alert alert-success bg-success text-white border-0 py-2 small text-center mb-3">
            <i class="fas fa-check-circle me-2"></i> <?= $_COOKIE['success']; ?>
        </div>
    <?php endif; ?>

    <?php if(isset($_COOKIE['error'])): ?>
        <div class="alert alert-danger bg-danger text-white border-0 py-2 small text-center mb-3">
            <i class="fas fa-exclamation-triangle me-2"></i> <?= $_COOKIE['error']; ?>
        </div>
    <?php endif; ?>

    <form id="resetForm" action="reset.php" method="POST">
        <div class="mb-3">
            <label class="text-uppercase fw-bold">Registered Email</label>
            <input type="email" name="email" class="form-control" placeholder="name@example.com">
        </div>

        <hr class="my-4 border-secondary">

        <div class="mb-3">
            <label class="text-uppercase fw-bold">New Password</label>
            <input type="password" name="new_password" id="new_password" class="form-control" placeholder="••••••••">
        </div>

        <div class="mb-4">
            <label class="text-uppercase fw-bold">Confirm New Password</label>
            <input type="password" name="confirm_password" class="form-control" placeholder="••••••••">
        </div>

        <button type="submit" name="reset_now" class="btn-red w-100 rounded-pill py-2 text-white">Update Password</button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>

<script>
$(document).ready(function() {
    // Alert auto-hide in 4 seconds
    setTimeout(function() {
        $(".alert").fadeOut('slow');
    }, 4000);

    $("#resetForm").validate({
        rules: {
            email: { required: true, email: true },
            new_password: { required: true, minlength: 6 },
            confirm_password: { required: true, equalTo: "#new_password" }
        },
        messages: {
            email: { required: "Email is required", email: "Enter valid email" },
            new_password: { required: "Enter new password", minlength: "Min 6 characters" },
            confirm_password: { required: "Confirm your password", equalTo: "Passwords don't match" }
        }
    });
});
</script>
</body>
</html>