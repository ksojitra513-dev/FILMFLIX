<?php
include '11_Config.php'; // Aapki connection file ($con variable yahan se aayega)

if (isset($_POST['save_user'])) {
    // Form se data receive karna aur safe banana
    $fullname  = mysqli_real_escape_string($con, $_POST['fullname']);
    $email     = mysqli_real_escape_string($con, $_POST['email']);
    $number    = mysqli_real_escape_string($con, $_POST['number']);
    $password  = mysqli_real_escape_string($con, $_POST['password']);
    $birthdate = mysqli_real_escape_string($con, $_POST['birthdate']);
    $city      = mysqli_real_escape_string($con, $_POST['City']);

    // Checkbox array ko string mein badalna
    $hobbies = isset($_POST['genres']) ? implode(", ", $_POST['genres']) : "";

    // Database mein data dalne ki query
    // Note: Column names wahi rakhein jo aapne table banate waqt diye the
    $query = "INSERT INTO user (fullname, email, number, password, birthdate, genres, city) 
              VALUES ('$fullname', '$email', '$number', '$password', '$birthdate', '$hobbies', '$city')";

    if (mysqli_query($con, $query)) {
        // --- YE LINE SABSE IMPORTANT HAI ---
        // Data save hote hi browser ko c.php par bhej do
        header("Location: c.php");
        exit(0);
    } else {
        echo "Data save nahi hua. Error: " . mysqli_error($con);
    }
} else {
    // Agar koi direct is page par aaye toh wapas registration par bhej do
    header("Location: register.php");
    exit(0);
}
