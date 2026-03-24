<?php
include '11_Config.php'; // Database connection file

if (isset($_POST['FullName'])) {
    // Data lena
    $name  = mysqli_real_escape_string($con, $_POST['FullName']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $phone = mysqli_real_escape_string($con, $_POST['number']);
    $pass  = mysqli_real_escape_string($con, $_POST['password']);
    $dob   = mysqli_real_escape_string($con, $_POST['Birthdate']);
    $city  = mysqli_real_escape_string($con, $_POST['City']);

    // Hobbies array ko string mein badalna
    $hobbies = isset($_POST['hobbies']) ? implode(", ", $_POST['hobbies']) : "";

    // SQL Query
    $query = "INSERT INTO user (fullname, email, number, password, birthdate, genres, city) 
              VALUES ('$name', '$email', '$phone', '$pass', '$dob', '$hobbies', '$city')";

    if (mysqli_query($con, $query)) {
        // SUCCESS: Ab automatic c.php khul jayega
        header("Location: c.php");
        exit();
    } else {
        echo "Error: " . mysqli_error($con);
    }
}
