<?php
// Line 2: Connection
$con = mysqli_connect("localhost", "root", "", "final");

if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}?>
<?php
date_default_timezone_set('Asia/Kolkata');
// બાકીનો ડેટાબેઝ કનેક્શન કોડ...
?>