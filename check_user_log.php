<?php
// Start Session First Thing
// Force script errors and warnings to show on page in case php.ini file is set to not display them

date_default_timezone_set ("Africa/Lagos");
error_reporting(E_ALL);
ini_set('display_errors', '1');
if (!isset($_SESSION['idx'])) {
    if (!isset($_COOKIE['emailCookie'])) {
        header("location: index.php");//go to login or index page
        exit();
    }
}


if (isset($_SESSION['idx'])) {
    //do nothing
}
else if (isset($_COOKIE['emailCookie'])) {// If email cookie is set, but no session ID is set yet, we set and update it below
    $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    
    $email =  mysqli_real_escape_string($conn, $_COOKIE['emailCookie']);
    $password_hash =  mysqli_real_escape_string($conn, $_COOKIE['passwordCookie']);
    
    $result = mysqli_query($conn, "SELECT * FROM student WHERE email = '$email' AND password = '$password_hash' AND activationstatus = 'ACTIVE' LIMIT 1");
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_array($result)) {
            $group_id = $row['groupid'];
            $member_type = $row['membertype'];

            $id = $group_id;
            $_SESSION['groupid'] = $group_id;
            $_SESSION['email'] = $email;
            $_SESSION['membertype'] = $member_type;
            $_SESSION['idx'] = base64_encode("g4p3h9xfn8sq03hs2234$id");
            
            setcookie("emailCookie", $email, $cookie_time , "/");
            setcookie("passwordCookie", $password_hash, $cookie_time, "/");
            setcookie("memberTypeCookie", $member_type, $cookie_time, "/");
            
        }

        header("location:$first_screen"); //Go to privileged page
        exit();
    }

    mysqli_close($conn);
}