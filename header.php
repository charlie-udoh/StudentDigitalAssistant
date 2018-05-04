<?php
isset($login) ? $login : $login=false;

require 'include/util.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>

 <title>SDA- <?php echo $page_title; ?></title>

<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="">
<meta name="author" content="">
<link rel="shortcut icon" href="assets/img/favicons/favicon.ico">
<!-- Normalize -->
<link rel="stylesheet" type="text/css" href="assets/css/normalize.css">
<!-- Bootstrap -->
<!--<link rel="stylesheet" type="text/css" href="assets/css/bootstrap.css">-->
<!-- Owl -->
<link rel="stylesheet" type="text/css" href="assets/css/owl.css">
<!-- Animate.css -->
<link rel="stylesheet" type="text/css" href="assets/css/animate.css">
<!-- Font Awesome -->
<link rel="stylesheet" type="text/css" href="assets/fonts/font-awesome-4.1.0/css/font-awesome.min.css">
<!-- Elegant Icons -->
<link rel="stylesheet" type="text/css" href="assets/fonts/eleganticons/et-icons.css">
<!-- Main style -->
<link rel="stylesheet" type="text/css" href="assets/css/cardio.css">

<!-- Bootstrap core CSS -->
<link href="assets/css/bootstrap.css" rel="stylesheet">

<!-- Custom styles for this template -->
<link href="assets/css/style.css" rel="stylesheet">
<link href="assets/css/font-awesome.min.css" rel="stylesheet">


<!-- Just for debugging purposes. Don't actually copy this line! -->
<!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->

<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->

  <link rel="stylesheet" href="assets/css/bootstrap.min.css">
  <script src="assets/js/jquery.min.js"></script>
  <script src="assets/js/bootstrap.min.js"></script>
<?php if ($login){?>
  <link rel="stylesheet" href="assets/css/css.css">
<?php }else{?>
  <link rel="stylesheet" href="assets/css/css_a.css">
<?php } ?>
</head>

<body>
<?php 
if(!$login){ ?>
<!-- Fixed navbar -->
<div class="navbar navbar-default navbar-fixed-top" role="navigation"  style="margin:0;">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="index.php">SMA</a>
        </div>
        <div class="navbar-collapse collapse navbar-right">
            <ul class="nav navbar-nav">
<!--                <li><a href="index.php">HOME</a></li>-->
                <li>
                    <?php

                    if(isset($_SESSION['idx']) || isset($_COOKIE['emailCookie'])){
                        if(isset($_SESSION['membertype']) && strtolower($_SESSION['membertype'])== 'admin') {
                            echo "<a href=\"setup_menu.php\">SETUP</a>";
                        }
                    }else{
                        echo "<a href=\"index.php\">HOME</a>";
                    }
                    ?>
                </li>
                <li>
                    <?php

                    if(isset($_SESSION['idx']) || isset($_COOKIE['emailCookie'])){
                        echo "<a href=\"logout.php\">SIGN OUT</a>";
                    }else{
                        echo "<a href='#' data-toggle='modal' data-target='#memberLoginModal'>SIGN IN</a>";
                    }
                    ?>
                </li>
            </ul>
        </div><!--/.nav-collapse -->
    </div>
</div>
<?php }?>
