<?php
session_start();
session_destroy();
// Simple redirect to a login page or home
header("Location: index.php");
exit();
?>
