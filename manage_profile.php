<?php
session_start();
include_once 'config.php';

if (isset($_POST['update_btn'])) {
    $u_id  = $_SESSION['user_id'];
    $name  = mysqli_real_escape_string($con, $_POST['name']);
    $phone = mysqli_real_escape_string($con, $_POST['phone']);
    $city  = mysqli_real_escape_string($con, $_POST['city']);

    // 1. Pehle simple data update karein
    $update_user = "UPDATE user SET fullname='$name', number='$phone', city='$city' WHERE id='$u_id'";
    $query_run = mysqli_query($con, $update_user);

    if ($query_run) {
        // 2. Image Handling (Directly in 'user' table update)
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['name'] != "") {
            
            $image_name = time() . '_' . $_FILES['profile_image']['name'];
            $target = "uploads/" . $image_name;

            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target)) {
                // User table mein image ka naam update karein
                mysqli_query($con, "UPDATE user SET image='$image_name' WHERE id='$u_id'");
            }
        }

        // --- SUCCESS MESSAGE (Using status=success for the popup) ---
        header("Location: profile.php?status=success");
        exit();
    } else {
        // Error handling
        header("Location: profile.php?status=error");
        exit();
    }
} else {
    header("Location: profile.php");
    exit();
}
?>