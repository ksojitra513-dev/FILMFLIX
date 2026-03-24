<?php
session_start();
include_once 'config.php'; // DB connection ($con)

if (isset($_POST['login_btn'])) {
    // Input ko clean karein
    $email = mysqli_real_escape_string($con, trim($_POST['email']));
    $password = trim($_POST['password']);

    // Check karein ki fields khali to nahi hain
    if (empty($email) || empty($password)) {
        $_SESSION['error'] = "Please fill all fields.";
        header("Location: login.php");
        exit();
    }

    // Query: Email ke base par user find karein
    $query = "SELECT * FROM registration WHERE email = '$email' LIMIT 1";
    $result = mysqli_query($con, $query);

    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);

        // Password Match (Agar aapne registration mein password_hash use kiya hai)
        // Agar plain password hai toh use karein: if($password == $user['password'])
        if ($password == $user['password']) { 
            
            // Status check (Optional but recommended)
            if (strtolower($user['status']) == 'active') {
                
                // Session Variables Set Karein
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_name'] = $user['fullname'];
                $_SESSION['role'] = $user['role'];

                // Dashboard par redirect karein
                header("Location: dashboard.php");
                exit();
            } else {
                $_SESSION['error'] = "Your account is inactive. Contact Admin.";
            }
        } else {
            $_SESSION['error'] = "Invalid Password!";
        }
    } else {
        $_SESSION['error'] = "No account found with this email.";
    }

    header("Location: login.php");
    exit();
}
?>