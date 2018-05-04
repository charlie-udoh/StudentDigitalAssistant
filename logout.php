<?php
include('include/util.php');
include ('include/common.php');
// Force script errors and warnings to show on page in case php.ini file is set to not display them
// Unset all of the session variables
$_SESSION = array();
// If it's desired to kill the session, also delete the session cookie
if (isset($_COOKIE['emailCookie'])) {
    setcookie("emailCookie", '', $cookie_expire_time, '/');
    setcookie("passwordCookie", '', $cookie_expire_time, '/');
    setcookie("memberTypeCookie", '', $cookie_expire_time, '/');
}
// Destroy the session variables
session_destroy();
header("location: index.php");
exit();

