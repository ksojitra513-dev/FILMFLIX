<?php
session_start();
include 'config.php';

if (isset($_POST['change_pwd_btn'])) {
    $u_id = $_SESSION['user_id'];
    $new_pass = $_POST['new_password'];
    $confirm_pass = $_POST['confirm_password'];

    if ($new_pass === $confirm_pass) {
        // Naye password ko HASH karna zaroori hai
        $hashed_pwd = password_hash($new_pass, PASSWORD_DEFAULT);

        // Query: Hashed password ko update karein
        $sql = "UPDATE registration SET password = '$hashed_pwd' WHERE id = '$u_id'";
        
        if (mysqli_query($con, $sql)) {
            // Password change hone ke baad session destroy karein
            session_unset();
            session_destroy();
            header("Location: login.php?msg=success"); // c.php ki jagah seedha login par
            exit();
            if (mysqli_query($con, $sql)) {
                
    // Session destroy nahi karna hai agar login rehna hai
    $_SESSION['status'] = "Password Updated Successfully!"; 
    header("Location: change_password.php"); // Wapis isi page par redirect
    exit();
}
        }
    } else {
        header("Location: dashboard.php?error=mismatch");
        exit();
    }
}
?>