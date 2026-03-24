<?php
session_start();
include_once '11_Config.php';

if (isset($_POST['login_btn'])) {
    $name    = mysqli_real_escape_string($con, $_POST['fullname']);
    $em      = mysqli_real_escape_string($con, $_POST['email']);
    $pwd     = mysqli_real_escape_string($con, $_POST['password']);
    $contact = mysqli_real_escape_string($con, $_POST['contact']);

    $q = "SELECT * FROM user WHERE email='$em' AND password='$pwd' AND fullname='$name' AND number='$contact' LIMIT 1";
    $result = mysqli_query($con, $q);

    if ($result && mysqli_num_rows($result) == 1) {
        $user_data = mysqli_fetch_assoc($result);

        if (strtolower($user_data['status']) == 'active') {
            // Dashboard synchronization
            $_SESSION['user_id']    = $user_data['id'];
            $_SESSION['user_email'] = $user_data['email'];
            $_SESSION['user_name']  = $user_data['fullname'];

            if (strtolower($user_data['role']) == 'admin') {
                $_SESSION['admin'] = $user_data['email'];
                header("Location: admin_dashboard.php");
            } else {
                header("Location: dashboard.php");
            }
            exit();
        } else {
            setcookie("error", "Account is not active.", time() + 5, "/");
        }
    } else {
        setcookie("error", "Details match nahi hui!", time() + 5, "/");
    }
    header("Location: 88_login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FLIMFLIX - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        html,
        body {
            background-color: #000;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            font-family: 'Segoe UI', sans-serif;
        }

        body {
            background: black url('all 1.jpg') no-repeat center center fixed !important;
            background-size: cover !important;
        }

        .form-container {
            padding-top: 60px;
            padding-bottom: 50px;
        }

        .card {
            background-color: rgba(255, 255, 255, 0.95) !important;
            border-radius: 15px;
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        }

        .card-header {
            background-color: #dc3545 !important;
            border-radius: 15px 15px 0 0 !important;
            color: white !important;
            padding: 20px;
        }

        .error {
            color: #dc3545 !important;
            font-size: 0.85rem;
            font-weight: bold;
            margin-top: 5px;
        }

        .is-invalid {
            border-color: #dc3545 !important;
        }
    </style>
</head>

<body>
    
    <br><br>

    <div class="container form-container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-5">

                <?php if (isset($_COOKIE['error'])): ?>
                    <div class="alert alert-danger text-center small fw-bold">
                        <?php echo $_COOKIE['error']; ?>
                    </div>
                <?php endif; ?>

                <div class="card shadow-lg">
                    <div class="card-header text-center">
                        <h4 class="mb-0 fw-bold">User Login</h4>
                    </div>

                    <div class="card-body p-4 p-md-5">
                        <form id="loginForm" action="" method="POST">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Full Name</label>
                                <input type="text" name="fullname" class="form-control" placeholder="Enter name">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Email</label>
                                <input type="email" name="email" class="form-control" placeholder="name@example.com">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Password</label>
                                <input type="password" name="password" class="form-control" placeholder="••••••••">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Contact No</label>
                                <input type="text" name="contact" class="form-control" placeholder="10 digit number">
                            </div>
                            <div class="d-grid mt-4">
                                <button type="submit" name="login_btn" class="btn btn-danger btn-lg fw-bold shadow-sm">Sign In</button>
                            </div>

                            <div class="text-center mt-4">
                                <p class="text-muted small mb-1">
                                    Don't have an account? <a href="88_r2.php" class="text-danger fw-bold text-decoration-none">Register Here</a>
                                </p>
                                <p class="small">
                                    <a href="88_forgot_password.php" class="text-secondary fw-bold text-decoration-none" style="font-size: 0.8rem;">Forgot Password?</a>
                                </p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.20.0/jquery.validate.min.js"></script>
    <script>
        $(document).ready(function() {
            $("#loginForm").validate({
                rules: {
                    fullname: {
                        required: true,
                        minlength: 3
                    },
                    email: {
                        required: true,
                        email: true
                    },
                    password: {
                        required: true,
                        minlength: 4
                    },
                    contact: {
                        required: true,
                        digits: true,
                        minlength: 10,
                        maxlength: 10
                    }
                },
                errorElement: 'div',
                errorPlacement: function(error, element) {
                    error.addClass('error');
                    element.closest('.mb-3').append(error);
                },
                highlight: function(element) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element) {
                    $(element).removeClass('is-invalid');
                }
            });
        });
    </script>
</body>

</html>