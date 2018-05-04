<?php
$login=true;
include ('include/util.php');
include ('include/common.php');
include('include/functions.php');


$this_file = htmlspecialchars($_SERVER["PHP_SELF"]);
$msgToUser = '';
$password = '';
$username = '';
$checked = '';

if(isset($_GET['_act_']) && !empty($_GET['_act_']) && $_GET['_act_'] == md5($active_value)){
	$msgToUser = '<span style="color: green">'. 'Log in with your email and password' .'</span>';
}


if( isset($_POST['origin']) && ($_POST['origin'] == 'login_all') && !empty($_POST['email_login_all']) && !empty($_POST['password_login_all']) ) {
	$username = mysqli_real_escape_string($conn, $_POST['email_login_all']);
	$password = mysqli_real_escape_string($conn, $_POST['password_login_all']);
	$password_hash = md5($password);
	$active = 'active';
	$status = '';
	$group_id = 0;
	$email = '';

	if(isset($_POST['remember_login_all'])){
		$checked = 1;
	}
	$result = mysqli_query($conn, "SELECT * FROM student WHERE email = '$username' AND password = '$password_hash' AND activationstatus = 'ACTIVE' LIMIT 1");
	if (mysqli_num_rows($result) > 0) {
		while ($row = mysqli_fetch_array($result)) {
			$group_id = $row['groupid'];
			$status = $row['activationstatus'];
			$member_type = $row['membertype'];
			$email = $row['email'];

			$id = $group_id;

			if (strtolower($status) == 'active') {
				$_SESSION['groupid'] = $group_id;
				$_SESSION['email'] = $email;
				$_SESSION['membertype'] = $member_type;
				$_SESSION['idx'] = base64_encode("g4p3h9xfn8sq03hs2234$id");

				if (isset($_POST['remember_login_all'])) {
					setcookie("emailCookie", $email, $cookie_time , "/");
					setcookie("passwordCookie", $password_hash, $cookie_time, "/");
					setcookie("memberTypeCookie", $member_type, $cookie_time, "/");
				}

				if(strtolower($member_type) == 'member') {
					header("location: $member_screen");
				}
				elseif(strtolower($member_type) == 'admin') {
					header("location: $first_screen");
				}
				
				exit();
			}else{
				$msgToUser = "<span style='color: red;'>Your account has not been activated</span>";
			}
		}

	} else {
		$msgToUser = "<span style='color: red;'>Email or password not recognized</span>";
	}
}

mysqli_close($conn);

include ('header.php');
include ('include/modals.php');
?>




<div class="container">
  <div class="row">
    <div class="col-sm-2">    </div>
    <div class="col-sm-4"><img src="assets/img/sma_s.png" />
<p class="main">STUDENT MOBILE ASSISTANT </p>
<p class="info">
Students typically have to keep track of any piece of information related to their course of study in order to fulfil their primary objective which is to graduate, and hopefully with a good grade too.
</p>
	<div style="width: 100%;">
	<button style="width: 40%;"  type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#registerModal"> Subscribe</button>
	</div>
    </div>
    <div class="col-sm-4 formlogin" style="border-left:1px dashed #082842;" >
<p class="LargeText">Sign In</p>
<!--Login Form-->
<form action="<?php echo $this_file; ?>" method="post" class="popup-form">
	<input type="text" name="email_login_all" class="form-control logintextbox" placeholder="Email" value="<?php echo $username; ?>">
	<input type="password" name="password_login_all" class="form-control logintextbox" placeholder="Password" value="<?php echo $password; ?>">
	<div class="checkbox-holder text-left rememberme" >
	<div class="checkbox">
		<input type="checkbox" <?php if($checked){echo 'checked'; } ?> value="None" id="squaredOne" name="remember_login_all" />
		<label for="squaredOne"><span><strong>Remember me</strong></span></label> <label for="squaredOne"><span><a href="#" data-toggle='modal' data-target='#forgotPasswordModal' style="color: darkblue;"> Forgot Password?</a></span></label>
	</div>
	</div>
	<div class="loginbuttonarea">
		<button type="submit" class="btn btn-primary btn-submit">Sign In</button>
		<input name="origin" type="hidden" value="login_all">

		<div style="margin-top: 15px; font-size: 14px; font-weight: bold;">
		<?php
		echo $msgToUser;
		?>
		</div>
	</div>

</form>

    </div>
    <div class="col-sm-1">    </div>
  </div>
</div>
<script src="assets/js/login.js"></script>
</body>
</html>


