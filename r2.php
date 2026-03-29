<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'config.php'; 
include 'mailer.php'; 

if(isset($_POST['save_user'])) {
    
    if (!$con) { die("Database connection failed: " . mysqli_connect_error()); }

    $fullname  = mysqli_real_escape_string($con, $_POST['FullName']);
    $email     = mysqli_real_escape_string($con, $_POST['email']);
    $number    = mysqli_real_escape_string($con, $_POST['number']);
    $password  = mysqli_real_escape_string($con, $_POST['password']); 
    $birthdate = $_POST['Birthdate'];
    $city      = $_POST['City'];
    $genres    = isset($_POST['hobbies']) ? implode(", ", $_POST['hobbies']) : "";

    // Image Upload Logic
    $image_name = "default.png"; 
    if(isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] == 0) {
        $target_dir = "uploads/";
        if (!file_exists($target_dir)) { mkdir($target_dir, 0777, true); }
        $image_name = time() . "_" . basename($_FILES["profile_photo"]["name"]);
        move_uploaded_file($_FILES["profile_photo"]["tmp_name"], $target_dir . $image_name);
    }

    // REPLACE INTO use kar rahe hain taaki testing mein email conflict na ho
    $query = "REPLACE INTO user (image, fullname, email, number, birthdate, password, genres, city, role, status) 
              VALUES ('$image_name', '$fullname', '$email', '$number', '$birthdate', '$password', '$genres', '$city', 'user', 'active')";
    
    if(mysqli_query($con, $query)) {
        
        // Welcome Email (Optional)
        $subject = "Welcome to FilmFlix!";
        $body = "<h2>Hi $fullname,</h2><p>Registration successful! Welcome to FilmFlix.</p>";
        sendEmail($email, $subject, $body);

        // --- CHANGE HERE: Redirect to c.php ---
        echo "<script>
                alert('Registration Successful!');
                window.location.href='login22.php';
              </script>";
        exit();
    } else {
        echo "Error: " . mysqli_error($con);
    }
}
?>