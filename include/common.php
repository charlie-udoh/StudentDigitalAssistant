<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

$cookie_time = time() + 60 * 60 * 24 * 100;
$cookie_expire_time =  time()-42000;
$active_value = 'activated';
$upload_folder = 'uploads';
$first_screen = 'setup_menu.php';
$member_screen = 'manage_uploads.php';