<?php
session_start();
include_once 'config.php';

if (isset($_POST['update_btn'])) {
    // 1. Get current user's email from session
    $session_email = $_SESSION['user']; 
    
    // 2. Collect and escape new data from form
    $newName  = mysqli_real_escape_string($con, $_POST['name']);
    $newPhone = mysqli_real_escape_string($con, $_POST['phone']);
    $newCity  = mysqli_real_escape_string($con, $_POST['city']);

    // 3. Update query
    $sql = "UPDATE user SET 
            FullName = '$newName', 
            number = '$newPhone', 
            city = '$newCity' 
            WHERE email = '$session_email'";

    if (mysqli_query($con, $sql)) {
        echo "<script>
                alert('Profile details updated successfully!');
                window.location.href = 'profile.php';
              </script>";
    } else {
        echo "Database Error: " . mysqli_error($con);
    }
} else {
    // If someone tries to access this file directly
    header("Location: profile.php");
    exit();
}
?>