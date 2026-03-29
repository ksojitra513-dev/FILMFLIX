<?php
// 1. Connection include karo
include_once '11_Config.php';

if (isset($_GET['email'])) {
    // Email ne clean karo jethi extra space ke SQL injection na thay
    $email = mysqli_real_escape_string($con, trim($_GET['email']));

    // Mam no Niyam: Check karo ke email registered chhe AND active chhe
    $query = "SELECT id FROM user WHERE email = '$email' AND status = 'active' LIMIT 1";
    $result = mysqli_query($con, $query);

    if ($result) {
        if (mysqli_num_rows($result) > 0) {
            // Email male chhe -> Button Enable thase
            echo "exists";
        } else {
            // Email nathi male -> Button Disable thase
            echo "not_exists";
        }
    } else {
        echo "error";
    }
}
