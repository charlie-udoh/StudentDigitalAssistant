<?php

require 'include/util.php';
include ('include/functions.php');
include ('include/common.php');
include ('check_user_log.php');

$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

if (!$conn) {
	die("Connection failed: " . mysqli_connect_error());
}
//$_SESSION['groupid']= 1;
$error_msg_email= array();
$send_mail_status= true;
$successful_invites= '';
$failed_invites= '';
$email_exist= '';
$email_invalid= '';
$err_msg= '';
$values= '';
$success_count= 0;
$failure_count= 0;
$msg='';
$error= false;

if(isset($_POST['submit']) && $_POST['submit'] == 'submit_invite') {
	echo '<script>
		$.blockUI({
			message: \'<h1 style=""><img style="" width="100" height="100" src="assets/img/ajax_loader_blue.gif" />Loading...</h1>\',
			css: {backgroundColor: \'#grey\', opacity: 0.3, color: \'#fff\', border: \'0px solid #C1DAD7\'};
</script>';
	$groupid= $_SESSION['groupid'];
	$adminname= '';
	$membertype= md5('member');

	if(empty($_POST['invite_list']) || $_POST['invite_list'] == '') {
		$error= true;
		$err_msg= 'Please enter email(s) in the box below';
	}

	if(!$error) {
		$_POST['invite_list']= validateInput($_POST['invite_list']);

		$email_arr= explode(',', $_POST['invite_list']);

		$sql= "select name from student where groupid= $groupid AND membertype= 'admin'";
		if($result=mysqli_query($conn, $sql)) {
			if (mysqli_num_rows($result) > 0) {
				$row= mysqli_fetch_assoc($result);
				$adminname= $row['name'];
			}
		}
		$subject= 'INVITATION';
		$title= $adminname.' just sent you an invite on SDA';
		$message= 'Please click the button below to accept the invitation';

		foreach($email_arr as $email) {
			$email= filter_var($email,FILTER_SANITIZE_EMAIL);
			if( filter_var($email,FILTER_VALIDATE_EMAIL) == false){
				array_push($error_msg_email, $email);
			}
			else{
				list($user, $domain) = explode("@", $email);

				if (!checkdnsrr($domain,"MX")) {
					array_push($error_msg_email, $email);
				}
				else {
					$email= trim($email);
					$valid_emaillist[]= $email;
				}
			}
		}

		if(empty($error_msg_email)) {
			require('PHPMailer/class.phpmailer.php');
			require('PHPMailer/class.smtp.php');

			foreach($valid_emaillist as $email) {
				if (! checkEmailExists($conn, strtolower($email), $_SESSION['groupid'])) {
					$invite_link= '<a href="'.APP_URL.'/activation_member.php?email='. $email. '&groupid='. $groupid. '&m_tp='.$membertype.'" style=\'color: white; font-size: larger; text-decoration: none; font-weight: bold;\'>ACCEPT INVITATION</a>';

					$mail_status= sendRequestApproveMail($conn, $email, $invite_link, $subject, $title, $message, $smtp_array);
					if($mail_status == 'success') {
						$successful_invites[]= $email;
						$success_count++;
					}
					elseif($mail_status == 'failure'){
						$failed_invites[]= $email;
						$failure_count++;
					}
				}
				else {
					$email_exist[]= $email;
				}

			}
			$msg= '<div class="alert alert-info"><span style="color: green;">'.$success_count.' invites successfully sent, </span><span style="color: red;">'.$failure_count.' invites not sent</span></div>';
		}
		else{
			$values= $_POST;
			foreach($error_msg_email as $invalid_email) {
				$email_invalid.= $invalid_email.', ';
			}
			$email_invalid= rtrim(trim($email_invalid), ',');
			$msg= '<div class="alert alert-info"><span style="color: red;">'.$email_invalid.' is invalid</span></div>';
		}
	}
	else {
		$msg='<div class="alert alert-info"><span style="color: red;">'.$err_msg.'</span></div>';
	}

}

//get user details
$user_details= getUserDetails($conn, $_SESSION['groupid']);

$page_title= 'Invite Members';
include('header_a.php');
?>

<!-- *****************************************************************************************************************
 AGENCY ABOUT
 ***************************************************************************************************************** -->

<div class="container mtb">
	<div class="row">
		<div class="col-lg-3">
			<?php include ("groupdetails.php");?>
			<h4>What would you like to do?</h4>
			<div class="list-group">
				<a href="setup_academic_year.php" class="list-group-item ">Setup Academic Year</a>
				<a href="setup_courses.php" class="list-group-item ">Setup Courses</a>
				<a href="invite_members.php" class="list-group-item active">Invite Members</a>
				<a href="upload_materials.php" class="list-group-item">Upload Materials</a>
				<a href="manage_uploads.php" class="list-group-item">Manage Uploads</a>
			</div>
		</div>
		<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
			<div class="modal-popup" style="color: white;">
				<h3>Invite Members</h3><br><br>
				<form action="<?php htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="" >
					<?php if($msg != "") echo $msg;?>
					<div class="form-group">
						<label for="invite_list">Enter email of invitee(s):</label>
						<textarea class="form-control" rows="10" id="invite_list" name="invite_list"><?php if($values) echo $values['invite_list']; ?></textarea>
						<p class="alert" style="background-color: #FFD700">please separate each email with comma (,) if more than one (1)</p>
					</div>
					<input type="hidden" value="submit_invite" name="submit" id="submit">
					<button type="submit" class="btn btn-submit">Invite</button>
				</form>
			</div>
		</div>
		<div class="col-lg-3">
			<?php if($successful_invites != '') { ?>
				<div class="well"><h4>Successful Invites</h4>
			<?php foreach($successful_invites as $invite) {
					echo '<span style="color:green;"><i class="fa fa-arrow-right"></i>'.$invite. '</span><br>';
				} ?>
				</div>
			<?php } ?>
			<?php if($failed_invites != '') { ?>
				<div class="well"><h4>Failed Invites</h4>
					<?php foreach($failed_invites as $invite) {
						echo '<span style="color:red;"><i class="fa fa-arrow-right"></i>'.$invite. '</span><br>';
					} ?>
				</div>
			<?php } ?>
			<?php if($email_exist != '') { ?>
				<div class="well"><h4>These members already exist:</h4>
					<?php foreach($email_exist as $invite) {
						echo '<span style="color:red;"><i class="fa fa-arrow-right"></i>'.$invite. '</span><br>';
					} ?>
				</div>
			<?php } ?>
		</div>
	</div><! --/row -->
</div><! --/container -->

<!-- *****************************************************************************************************************
 FOOTER
 ***************************************************************************************************************** -->
<?php include('footer.php') ?>

<script>
		$.blockUI({
			message: '<h1 style=""><img style="" width="100" height="100" src="assets/img/ajax_loader_blue.gif" />Loading...</h1>',
			css: {backgroundColor: '#grey', opacity: 0.3, color: '#fff', border: '0px solid #C1DAD7'};
</script>
</body>
</html>
