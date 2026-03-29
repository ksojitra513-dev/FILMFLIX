<?php
include 'config.php';

echo "<div style='font-family: Arial; text-align: center; margin-top: 50px;'>";

if (isset($_GET['token']) && isset($_GET['em'])) {
    $token = mysqli_real_escape_string($con, $_GET['token']);
    $email = mysqli_real_escape_string($con, $_GET['em']);

    // Check user with this token and email
    $q = "SELECT * FROM user WHERE email='$email' AND token='$token'";
    $res = mysqli_query($con, $q);
    
    if (mysqli_num_rows($res) > 0) {
        // Update status to 'active' and clear token
        $update = "UPDATE user SET status='active', token='' WHERE email='$email'";
        
        if (mysqli_query($con, $update)) {
            echo "<h1 style='color: green;'>✔ Account Activated!</h1>";
            echo "<p>Ab aap login kar sakte hain.</p>";
            echo "<a href='login.php' style='padding:10px; background:#dc3545; color:white; text-decoration:none;'>Login Now</a>";
        } else {
            echo "Error updating status: " . mysqli_error($con);
        }
    } else {
        echo "<h1 style='color: orange;'>⚠ Link Invalid or Already Used!</h1>";
    }
} else {
    echo "<h1>Invalid Access!</h1>";
}
echo "</div>";
?>