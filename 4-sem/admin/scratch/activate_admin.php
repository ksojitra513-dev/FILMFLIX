<?php
require_once '../config.php';
$res = mysqli_query($con, "UPDATE user SET status='active' WHERE email='admin@filmflix.com'");
if($res) echo "Admin account activated!";
else echo "Error: " . mysqli_error($con);
?>
