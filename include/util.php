<?php
if (session_status() == PHP_SESSION_NONE) {
	session_start();
}

//PATH CONFIGURATION
$directory = realpath(dirname(__FILE__));
$document_root = realpath($_SERVER['DOCUMENT_ROOT']);
$base_url = ( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on' ? 'https' : 'http' ) . '://' . $_SERVER['HTTP_HOST'];
if(strpos($directory, $document_root)===0) {
	$base_url .= str_replace(DIRECTORY_SEPARATOR, '/', substr($directory, strlen($document_root)));
}

defined("APP_URL") ? null : define("APP_URL", str_replace("/include", "", $base_url));

//HOST NAME
$server_host= $_SERVER['HTTP_HOST'];

//DATABASE CONFIGURATION
defined("DB_HOST") ? null : define("DB_HOST", "127.0.0.1");
defined("DB_USER") ? null : define("DB_USER", "phpuser");
defined("DB_PASSWORD") ? null : define("DB_PASSWORD", "pa55w0rd@1");
defined("DB_NAME") ? null : define("DB_NAME", "sda");


//defined("DB_HOST") ? null : define("DB_HOST", "127.0.0.1");
//defined("DB_USER") ? null : define("DB_USER", "dqdemosc_sda");
//defined("DB_PASSWORD") ? null : define("DB_PASSWORD", "Test123*#");
//defined("DB_NAME") ? null : define("DB_NAME", "dqdemosc_sda");

$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);


//EMAIL CONFIGURATION

//development email settings (localhost)
$smtp_array= array(
	'smtp_host' => "smtp.gmail.com",
	'smtp_username' => "bluetagautomailer@gmail.com",
	'smtp_password' => "pa55w0rd@1",
	'smtp_port'=> "465"
);

//production email settings (online)
// $smtp_array= array(
	// 'smtp_host'=> "just45.justhost.com",
	// 'smtp_username'=> "automailer@dqdemos.com",
	// 'smtp_password'=> "Test123*#",
	// 'smtp_port'=> "465"
// );

$academic_programs = array(
	'mphil_phd'=> "MPHIL/PHD",
	'mba_msc'=> "MBA/MSc",
	'mbbs'=> "MBBS",
	'bsc_ba_beng'=> 'BA/BEng/BSc',
	'pgd'=>'Post Graduate Diploma',
	'hnd'=> "HND",
	'ond'=> "OND",
	'nce'=> "NCE",
	'diploma'=> "DIPLOMA",
	'high_sch'=> "HIGH SCHOOL",
	'vocational'=> "VOCATIONAL",
	'others'=> "OTHERS"
);
