<?php
require_once 'config.php';

$email = 'admin@filmflix.com';
$password = 'admin123';
$fullname = 'Super Admin';

// Check if admin already exists
$check = mysqli_query($con, "SELECT id FROM user WHERE email = '$email' AND role = 'admin'");

if (mysqli_num_rows($check) == 0) {
    // Insert admin user
    $query = "INSERT INTO user (fullname, email, number, birthdate, password, role, status) 
              VALUES ('$fullname', '$email', '9999999999', '1990-01-01', '$password', 'admin', 'active')";
    if (mysqli_query($con, $query)) {
        echo "Admin user created successfully.\n";
        echo "Email: $email\n";
        echo "Password: $password\n";
    } else {
        echo "Error creating admin user: " . mysqli_error($con) . "\n";
    }
} else {
    echo "Admin user already exists.\n";
    echo "Email: $email\n";
    echo "Password: (unchanged)\n";
}
?>
