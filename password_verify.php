<?php
session_start();
include_once 'config.php'; 

if (isset($_POST['login_btn'])) {
    $em  = mysqli_real_escape_string($con, $_POST['email']);
    $pwd = mysqli_real_escape_string($con, $_POST['password']); 

    // Ab query direct password match karegi
    $q = "SELECT * FROM user WHERE email='$em' AND password='$pwd' LIMIT 1";
    $result = mysqli_query($con, $q);
    
    if ($result && mysqli_num_rows($result) == 1) {
        $user_data = mysqli_fetch_assoc($result);
        $_SESSION['user_id'] = $user_data['id']; 
        header("Location: dashboard.php");
        exit();
    } else {
        setcookie("error", "Email ya Password galat hai!", time() + 5, "/");
        header("Location: login.php");
        exit();
    }
}
?>