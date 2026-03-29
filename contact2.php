<?php
include 'config.php'; // Database connection include karein

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Form se data lena aur safe banana
    $name = mysqli_real_escape_string($con, $_POST['FullName']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $msg = mysqli_real_escape_string($con, $_POST['Message']);

    // 2. INSERT Query (Jo aapke code mein miss ho rahi thi)
    $query = "INSERT INTO contact (fullname, email, message) VALUES ('$name', '$email', '$msg')";

    // 3. Query run karna aur redirect karna
    if (mysqli_query($con, $query)) {
        echo "<script>
                alert('Success! Message database mein save ho gaya.');
                window.location.href = 'c.php'; 
              </script>";
    } else {
        // Agar table ya column name galat hua toh yahan dikhega
        echo "Error: " . mysqli_error($con);
    }
}
?>