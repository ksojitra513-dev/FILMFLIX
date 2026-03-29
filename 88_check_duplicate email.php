<?php
// 1. Connection file include karo
include_once '11_Config.php';

// GET parameter nu naam 'email1' j rakhyu chhe jem tame moklyu hatu
if (isset($_GET['email1'])) {

    // Email ne clean karo jethi SQL injection na thay
    $email = mysqli_real_escape_string($con, trim($_GET['email1']));

    // Mam no Niyam: Fakt Active users ne j OTP javvo joie
    // Table: registration, Column: email
    $sql = "SELECT id FROM user WHERE email = '$email' LIMIT 1";
    $result = mysqli_query($con, $sql);

    if ($result === false) {
        // Query fail thay to blank response na aave e mate
        echo 'error';
        exit();
    }

    // Jo record male (Rows > 0), to matlab ke email registered chhe
    if (mysqli_num_rows($result) > 0) {
        // Email database ma chhe -> OTP mokli shakase
        echo 'exists';
    } else {
        // Email database ma nathi -> Error batavvi padse
        echo 'not_exists';
    }

    mysqli_free_result($result);
} else {
    echo 'no_parameter';
}
