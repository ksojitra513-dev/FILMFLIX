<?php
require_once 'config.php';

$query = "SELECT id, fullname, email, role, status FROM user";
$result = mysqli_query($con, $query);

if ($result) {
    echo "Total users: " . mysqli_num_rows($result) . "\n\n";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "ID: " . $row['id'] . " | Name: " . $row['fullname'] . " | Email: " . $row['email'] . " | Role: " . $row['role'] . " | Status: " . $row['status'] . "\n";
    }
} else {
    echo "Error: " . mysqli_error($con);
}
?>
