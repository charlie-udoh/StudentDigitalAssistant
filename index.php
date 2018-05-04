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
	$msgToUser = '<span style="color: white">'. 'Log in with your email and password' .'</span>';
}


if( isset($_POST['origin']) && ($_POST['origin'] == 'login_all')) {
	if(empty($_POST['email_login_all'])) {
		$msgToUser= '<span style=\'color: red;\'>Please provide email</span>';
	}
	elseif (empty($_POST['password_login_all'])) {
		$msgToUser= '<span style=\'color: red;\'>Please provide password</span>';
	}
	else {
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

}
mysqli_close($conn);

$page_title= "Welcome to SDA";
include ('header.php');
include ('include/modals.php'); echo $username; echo $password;
?>




<div class="container">
	<div class="row">
		<div class="col-sm-2">    </div>
		<div class="col-sm-4" style="padding-bottom:20px;" align="center"><img src="assets/img/logo_white.png" />
			<p class="main">STUDENT MOBILE ASSISTANT </p>
			<p>A service that utilises mobile technology to aid students in keeping track of all the semester course related materials.</p>
			<div style="width: 100%;" align="center">
				<button style="width: 60%;"  type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#registerModal"> Subscribe</button>
			</div>
		</div>
		<div class="col-sm-4 formlogin" style="border:1px solid #FFFFFF;background-image:url(assets/img/blue_d.png);" >
			<p class="LargeText" style="color:white;">Sign In</p>
			<!--Login Form-->
			<form action="<?php echo $this_file; ?>" method="post" class="popup-form" id="student_login_form">
				<div style="text-align: center; font-size: 14px; font-weight: bold;">
					<?php
					echo $msgToUser;
					?>
				</div>
				<input type="text" name="email_login_all" class="form-control" placeholder="Email" value="<?php if(isset($_POST['email_login_all'])) echo $_POST['email_login_all']; ?>">
				<input type="password" name="password_login_all" class="form-control" placeholder="Password" value="<?php if(isset($_POST['password_login_all'])) echo $_POST['password_login_all']; ?>">
				<div class="checkbox-holder text-left rememberme" >
					<div class="checkbox">
						<input type="checkbox" <?php if($checked){echo 'checked'; } ?> value="None" id="squaredOne" name="remember_login_all" />
						<label for="squaredOne"><span>Remember me</span></label> <label for="squaredOne"><span><a href="#" data-toggle='modal' data-target='#forgotPasswordModal' style="color: white;"> Forgot Password?</a></span></label>
					</div>
				</div>
				<div class="loginbuttonarea">
					<button type="submit" class="btn btn-primary btn-submit">Sign In</button>
					<input name="origin" type="hidden" value="login_all">
				</div>
			</form>
		</div>
	</div>
</div>
<?php include ('footer.php'); ?>
<script src="assets/js/login.js"></script>
</body>
</html>


