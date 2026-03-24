<?php
session_start();
include_once 'config.php'; 

if (isset($_POST['login_btn'])) {
    $name    = mysqli_real_escape_string($con, $_POST['fullname']);
    $em      = mysqli_real_escape_string($con, $_POST['email']);
    $pwd     = mysqli_real_escape_string($con, $_POST['password']); 
    $contact = mysqli_real_escape_string($con, $_POST['contact']);

    // Plain Text match (Kyuki aapne hashing hata di hai)
    $q = "SELECT * FROM user WHERE email='$em' AND password='$pwd' LIMIT 1";
    $result = mysqli_query($con, $q);
    
    if ($result && mysqli_num_rows($result) == 1) {
        $user_data = mysqli_fetch_assoc($result);
        
        // Name aur Contact bhi match karein
        if ($user_data['fullname'] == $name && $user_data['number'] == $contact) {
            
            if (strtolower($user_data['status']) == 'active') {
                $_SESSION['user_id']    = $user_data['id']; 
                $_SESSION['user_email'] = $user_data['email'];
                $_SESSION['user_name']  = $user_data['fullname'];

                header("Location: " . (strtolower($user_data['role']) == 'admin' ? "admin_dashboard.php" : "dashboard.php"));
                exit(); 
            } else {
                setcookie("error", "Account is not active.", time() + 5, "/");
            }
        } else {
            setcookie("error", "Name ya Contact details match nahi hui!", time() + 5, "/");
        }
    } else {
        setcookie("error", "Email ya Password galat hai!", time() + 5, "/");
    }
    header("Location: login.php");
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        html, body { background-color: #000; margin: 0; padding: 0; min-height: 100vh; font-family: 'Segoe UI', sans-serif; }
        body { background: black url('all 1.jpg') no-repeat center center fixed !important; background-size: cover !important; }
        
        /* HEADER SE DOOR KARNE KE LIYE YAHAN CHANGE KIYA HAI */
        .form-container { 
            padding-top: 120px; /* Isse header se gap badh jayega */
            padding-bottom: 50px; 
        }

        .card { 
            background-color: rgba(255, 255, 255, 0.95) !important; 
            border-radius: 15px; border: none; 
            box-shadow: 0 15px 35px rgba(0,0,0,0.7); 
        }
        .card-header { background-color: #e50914 !important; border-radius: 15px 15px 0 0 !important; color: white !important; padding: 20px; }
        .error { color: #dc3545 !important; font-size: 0.85rem; font-weight: bold; margin-top: 5px; }
        .is-invalid { border-color: #dc3545 !important; }
        .btn-danger { background-color: #e50914; border: none; transition: 0.3s; }
        .btn-danger:hover { background-color: #b20710; transform: translateY(-2px); }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    <?php if(isset($_GET['msg']) && $_GET['msg'] == 'success'): ?>
    <div id="passAlert" class="alert alert-success alert-dismissible fade show shadow-sm mb-4 text-center" 
         style="border-left: 5px solid #198754; background-color: #d1e7dd; color: #0f5132;">
        <i class="fas fa-check-circle me-2"></i> 
        <strong>Password Changed Successfully!</strong> <br>
        <small>Ab aap apne naye password ke saath login kar sakte hain.</small>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>

    <script>
        setTimeout(function() {
            var alert = document.getElementById('passAlert');
            if(alert) {
                var bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }
        }, 5000);
    </script>
<?php endif; ?>
    
    <div class="container form-container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-5">
                
                <?php if(isset($_COOKIE['error'])): ?>
                    <div class="alert alert-danger text-center small fw-bold mb-4">
                        <i class="fas fa-exclamation-triangle me-2"></i> <?php echo $_COOKIE['error']; ?>
                    </div>
                <?php endif; ?>

                <div class="card shadow-lg">
                    <div class="card-header text-center">
                        <h4 class="mb-0 fw-bold"><i class="fas fa-user-lock me-2"></i>User Login</h4>
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
                                <p class="mb-2">
                                    <a href="forget.php" class="text-muted small text-decoration-none">
                                        <i class="fas fa-question-circle me-1"></i> Forgot Password?
                                    </a>
                                </p>
                                <p class="text-muted small mb-0">
                                    Don't have an account? <a href="r2.php" class="text-danger fw-bold text-decoration-none">Register Here</a>
                                </p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php include 'footer.php'; ?>
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
        // Custom Messages yahan add kiye hain
        messages: {
            fullname: {
                required: "Please enter a fullname.",
                minlength: "Name must be at least 3 characters long."
            },
            email: {
                required: "Please enter your email address.",
                email: "Please enter a valid email."
            },
            password: {
                required: "Please enter your password.",
                minlength: "Password must be at least 4 characters."
            },
            contact: {
                required: "Please enter your contact number.",
                digits: "Please enter only numbers.",
                minlength: "Contact must be 10 digits.",
                maxlength: "Contact must be 10 digits."
            }
        },
        errorElement: 'div',
        errorPlacement: function (error, element) {
            error.addClass('error');
            element.closest('.mb-3').append(error);
        },
        highlight: function (element) { $(element).addClass('is-invalid'); },
        unhighlight: function (element) { $(element).removeClass('is-invalid'); }
    });
});
</script>

</body>
</html>