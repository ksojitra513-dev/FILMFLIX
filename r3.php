<?php
include 'config.php'; 

if (isset($_POST['FullName'])) {
    $name      = mysqli_real_escape_string($con, $_POST['FullName']);
    $email     = mysqli_real_escape_string($con, $_POST['email']);
    $number    = mysqli_real_escape_string($con, $_POST['number']);
    
    // Hashing Hata Di: Ab direct password save hoga
    $password  = mysqli_real_escape_string($con, $_POST['password']); 

    $birthdate = mysqli_real_escape_string($con, $_POST['Birthdate']);
    $city      = mysqli_real_escape_string($con, $_POST['City']);
    $hobbies   = isset($_POST['hobbies']) ? implode(", ", $_POST['hobbies']) : "";

    $image_name = ""; 
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
        $image_name = time() . "_" . $_FILES['profile_image']['name'];
        move_uploaded_file($_FILES['profile_image']['tmp_name'], "uploads/" . $image_name);
    }

    $query = "REPLACE INTO registration (fullname, email, number, password, birthdate, genres, city, image) 
              VALUES ('$name', '$email', '$number', '$password', '$birthdate', '$hobbies', '$city', '$image_name')";

    if (mysqli_query($con, $query)) {
        header("Location: c.php");
        exit();
    }
}
?>