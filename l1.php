<?php
session_start();
include_once 'config.php';

if (isset($_POST['login_btn'])) {
    $em = trim($_POST['email']);
    $pwd = $_POST['password'];

    // Prepared Statement for Security
    $stmt = $con->prepare("SELECT * FROM registration WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $em);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user_data = $result->fetch_assoc();

        // 1. Password Match (Plain text jaisa aapne pucha)
        if ($pwd === $user_data['password']) {
            
            // 2. Status Check (Flexible: Agar status set nahi hai toh bhi active maane)
            $status = isset($user_data['status']) ? strtolower($user_data['status']) : 'active';
            
            if ($status === 'active' || $status === '') {
                
                // Session data set karein
                $_SESSION['user_id'] = $user_data['id'];
                $_SESSION['user_email'] = $user_data['email'];
                $_SESSION['user_name'] = $user_data['fullname']; // Register table se naam

                // 3. Role-Based Redirect
                $role = isset($user_data['role']) ? strtolower($user_data['role']) : 'user';

                if ($role === 'admin') {
                    $_SESSION['admin'] = $user_data['email'];
                    setcookie('success', "Admin Login Success", time() + 5, "/");
                    header("Location: admin_dashboard.php");
                } else {
                    $_SESSION['user'] = $user_data['email'];
                    setcookie('success', "Login Success", time() + 5, "/");
                    header("Location: profile.php");
                }
                exit();

            } else {
                setcookie("error", "Account is " . $status . ". Contact Admin.", time() + 5, "/");
                header("Location: login.php");
                exit();
            }

        } else {
            setcookie("error", "Incorrect password.", time() + 5, "/");
            header("Location: login.php");
            exit();
        }
    } else {
        setcookie("error", "Email not found in registration table.", time() + 5, "/");
        header("Location: login.php");
        exit();
    }
}
?>