<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

include_once 'config.php'; 

if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

// --- BACKEND LOGIC ---
if (isset($_POST['reset_now'])) {
    $email = mysqli_real_escape_string($con, trim($_POST['email']));
    $new_pass = mysqli_real_escape_string($con, $_POST['new_password']);

    $check_email = "SELECT * FROM user WHERE email = '$email'";
    $result = mysqli_query($con, $check_email);

    if (mysqli_num_rows($result) > 0) {
        // Password update query
        $update_query = "UPDATE user SET password = '$new_pass' WHERE email = '$email'";
        
        if (mysqli_query($con, $update_query)) {
            $_SESSION['success_msg'] = "Password Reset Successful! Please Login.";
            header("Location: login.php");
            exit();
        } else {
            $_SESSION['error'] = "Database Error. Try again.";
        }
    } else {
        $_SESSION['error'] = "This email is not registered with us!";
    }
    header("Location: reset.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - FILMFLIX</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        body { 
            background-color: #000 !important; 
            color: white; 
            font-family: 'Segoe UI', sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }
        .reset-card {
            background: #141414;
            padding: 40px;
            border-radius: 8px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
            border: 1px solid #222;
        }
        .brand-logo {
            color: #e50914 !important;
            font-size: 2.2rem;
            font-weight: 800;
            text-decoration: none;
            display: block;
            text-align: center;
            margin-bottom: 25px;
        }
        .form-control {
            background: #333 !important;
            border: 1px solid #444 !important;
            color: white !important;
            padding: 12px;
        }
        .form-control:focus {
            box-shadow: none;
            border-color: #e50914 !important;
        }
        .btn-reset {
            background: #e50914 !important;
            border: none;
            font-weight: 600;
            padding: 12px;
            margin-top: 10px;
        }
        .btn-reset:hover { background: #b20710 !important; }
        
        /* Validation Error Style */
        .error {
            color: #e50914;
            font-size: 0.8rem;
            margin-top: 5px;
            display: block;
        }
        input.error {
            border: 1px solid #e50914 !important;
        }
    </style>
</head>
<body>
<?php include 'header.php'; ?>
<div class="reset-card">
    <a href="index.php" class="brand-logo">FILMFLIX</a>
    <h4 class="text-center mb-4">Reset Password</h4>

    <?php if(isset($_SESSION['error'])): ?>
        <div class="alert alert-danger bg-danger text-white border-0 py-2 small">
            <i class="fas fa-exclamation-circle me-2"></i> <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <form id="resetForm" action="reset.php" method="POST">
        <div class="mb-3">
            <label class="small text-secondary mb-1">Registered Email</label>
            <input type="email" name="email" class="form-control" placeholder="example@mail.com">
        </div>

        <div class="mb-3">
            <label class="small text-secondary mb-1">New Password</label>
            <input type="password" id="new_password" name="new_password" class="form-control" placeholder="At least 6 characters">
        </div>

        <button type="submit" name="reset_now" class="btn btn-danger w-100 btn-reset">
            UPDATE PASSWORD
        </button>
    </form>

    <div class="text-center mt-4">
        <a href="login.php" class="text-secondary small text-decoration-none hover-white">Back to Login</a>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>

<script>
$(document).ready(function () {
    $("#resetForm").validate({
        rules: {
            email: {
                required: true,
                email: true
            },
            new_password: {
                required: true,
                minlength: 6
            }
        },
        messages: {
            email: {
                required: "Email is required to reset password",
                email: "Please enter a valid email address"
            },
            new_password: {
                required: "Please enter a new password",
                minlength: "Password must be at least 6 characters"
            }
        },
        errorClass: "error",
        // Client-side validation hote hi button ko thoda feedback dena (Optional)
        submitHandler: function(form) {
            $(".btn-reset").html('<i class="fas fa-spinner fa-spin"></i> Updating...');
            form.submit();
        }
    });
});
</script>

</body>
</html>