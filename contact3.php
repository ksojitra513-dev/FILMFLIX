<?php
include 'config.php'; // Connection file

$sql = "CREATE TABLE IF NOT EXISTS contact (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(100),
    email VARCHAR(100),
    message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if (mysqli_query($con, $sql)) {
    echo "<h3>Success! 'contact' table ban gaya hai.</h3>";
    echo "<a href='contact.php'>Ab Contact Page test karein</a>";
} else {
    echo "Error creating table: " . mysqli_error($con);
}
?>