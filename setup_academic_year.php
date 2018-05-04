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
$error= false;
$success= false;
$error_msg_db= "";
$error_msg_year= "";
$error_msg_semester= "";
$del_msg= '';

if (isset($_POST['submit']) && $_POST['submit'] == 'submit_academic_year') {
	$_POST['status']= 'ACTIVE';
	$_POST['datecreated']= date('Y-m-d');
	if($_POST['select_year'] == "") {
		$error= true;
		$error_msg_year= '<div class="help-block" style="color: red; background-color: white;">Please select academic year</div>';
	}
	if($_POST['select_semester'] == "") {
		$error= true;
		$error_msg_semester= '<div class="help-block" style="color: red; background-color: white;">Please select semester</div>';
	}

	if(!$error){
		$_POST['select_year']= validateInput($_POST['select_year']);
		$_POST['select_semester']= validateInput($_POST['select_semester']);
		$_POST['academicperiod']= $_POST['select_year'].$_POST['select_semester'];
		if(checkRecordExists($conn, $_POST, 'academicperiod')) {
			$error= true;
			$error_msg_db= '<div class="alert alert-danger">The academic Period you have selected already exists</div>';
		}
		else {
			if(!$last_inserted= insertRecord($conn, $_POST, 'academicperiod')) {
				$error= true;
				$error_msg_db= '<div class="alert alert-danger">An error occurred while inserting record</div>';
			}
			else {
				$academic_year_record= getAcademicYear($conn,0, $last_inserted);
				
				$root_dir = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR;
				$academic_year_dir= DIRECTORY_SEPARATOR.$academic_year_record['academicperiodid']. DIRECTORY_SEPARATOR;
				$group_dir_arr= glob($root_dir.'*', GLOB_ONLYDIR);
				
				foreach ($group_dir_arr as $dir) {
					$dir_name= basename($dir);
					if($dir_name == $academic_year_record['groupid']) {
						if(!file_exists($dir.$academic_year_dir)) {
							@mkdir($dir.$academic_year_dir);
						}
					}
				}
				$success= true;
				$success_msg= '<div class="alert alert-success">Academic Year has been created successfully</div>';
			}
		}
	}
}

if(isset($_POST['academicperiod_id_hidden']) && isset($_POST['group_id_hidden'])) {
	$academic_period_deleted= getAcademicYear($conn, $_POST['group_id_hidden'], $_POST['academicperiod_id_hidden']);
	$dir = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR.$_POST['group_id_hidden'].DIRECTORY_SEPARATOR.$_POST['academicperiod_id_hidden'];

	if(! deleteDirectory($dir)) {
		$del_msg= '<p class="alert alert-danger">An error occurred while deleting academic period <b>'.$academic_period_deleted['academicperiod'].'</b></p>';
	}
	else {
		if(deleteRecord($conn, $_POST['academicperiod_id_hidden'], 'academicperiod', '', 'course')) {
			if (!deleteRecord($conn, $_POST['academicperiod_id_hidden'], 'academicperiod')) {
				$del_msg = '<p class="alert alert-danger">An error occurred while deleting academic period <b>' . $academic_period_deleted['academicperiod'] . '</b></p>';
			} else {
				$del_msg = '<p class="alert alert-success">Academic period <b>' . $academic_period_deleted['academicperiod'] . '</b> deleted successfully</p>';
			}
		}
		else {
			$del_msg = '<p class="alert alert-danger">An error occurred while deleting academic period <b>' . $academic_period_deleted['academicperiod'] . '</b></p>';
		}
	}
}
//get user details
$user_details= getUserDetails($conn, $_SESSION['groupid']);

//get Academic periods for group

if(!$academic_periods= getAcademicYear($conn, $_SESSION['groupid'])) {
	$academic_list= '<p>You have not set up any academic period yet';
}
else {
	$academic_list = '<table class="table table-hovered table-striped">
				<thead>
				<tr>
					<th>Academic Year</th>
					<th>Delete</th>
				</tr>
				</thead>
				<tbody>';
	foreach ($academic_periods as $academic_period) {
		$academic_list .= '<tr>
					    <td>' . $academic_period['academicperiod'] . '</td>
						<td>
							<button class="btn btn-sm btn-danger button-manage" type=\'button\'  title=\'Delete\' data-toggle=\'modal\' data-target=\'#deleteModal\' data-original-title=\'Delete\' onclick=\'confirmDeletePeriod(this)\'>
								<input type="hidden" name="academicperiodid" class="period_del" id="academicperiodid" value="' . $academic_period['academicperiodid'] . '">
								<input type="hidden" name="groupid" class="group_del" id="groupid" value="' . $academic_period['groupid'] . '">
								<i class="fa fa-times-circle" ></i>
							</button>
						</td>
					</tr>';
	}
	$academic_list.='</tbody></table>';
}
$select_year_options= '<option value="">--Select Year--</option>';
$select_year_options_arr= array('Year 1'=> 'Year1', 'Year 2'=> 'Year2', 'Year 3'=> 'Year3', 'Year 4'=> 'Year4', 'Year 5'=> 'Year5', 'Year 6'=> 'Year6');
foreach($select_year_options_arr as $option=> $value) {
	if(isset ($_POST['select_year'])) {
		if($value == $_POST['select_year']) {
			$selected= 'selected';
		}
		else {
			$selected= '';
		}
	}
	else {
		$selected= '';
	}
	$select_year_options.= '<option value="'.$value.'" '.$selected.'>'.$option.'</option>';
}

$select_semester_options= '<option value="">--Select Semester--</option>';
$select_semester_options_arr= array('Semester 1'=> 'Semester1', 'Semester 2'=> 'Semester2', 'Semester 3'=> 'Semester3');
foreach($select_semester_options_arr as $option=> $value) {
	if(isset ($_POST['select_semester'])) {
		if($value == $_POST['select_semester']) {
			$selected= 'selected';
		}
		else {
			$selected= '';
		}
	}
	else {
		$selected= '';
	}
	$select_semester_options.= '<option value="'.$value.'" '.$selected.'>'.$option.'</option>';
}


$page_title= 'Setup Academic Year';
include ('header_a.php');
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
				<a href="setup_academic_year.php" class="list-group-item active">Setup Academic Year</a>
				<a href="setup_courses.php" class="list-group-item ">Setup Courses</a>
				<a href="invite_members.php" class="list-group-item">Invite Members</a>
				<a href="upload_materials.php" class="list-group-item">Upload Materials</a>
				<a href="manage_uploads.php" class="list-group-item">Manage Uploads</a>
			</div>
		</div>
		<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
			<div class="modal-popup" style="color: white;">
				<h3>Setup Academic Period</h3><br><br>
				<?php if($success) echo $success_msg; if($error_msg_db != "") echo $error_msg_db; ?>
				<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" class="" method="post">
					<div class="form-group">
						<label for="select_year">Academic Year:</label>
						<select class="form-control" id="select_year" name="select_year">
							<?php echo $select_year_options; ?>
						</select>
						<?php if($error && $error_msg_year != "") { echo $error_msg_year; } ?>
					</div>
					<div class="form-group">
						<label for="select_semester">Semester:</label>
						<select class="form-control" id="select_semester" name="select_semester">
							<?php echo $select_semester_options; ?>
						</select>
						<?php if($error && $error_msg_semester != "") {echo $error_msg_semester;} ?>
					</div>

					<input type="hidden" name="groupid" id="groupid" value="<?php echo $_SESSION['groupid']; ?>">
					<input type="hidden" value="submit_academic_year" name="submit" id="submit">
					<button type="submit" class="btn btn-submit">Submit</button>
				</form>
			</div>
		</div>
		<div class="col-lg-3">
			<h4>Manage Academic Year</h4>
			<?php echo  $del_msg; ?>
			<?php echo $academic_list;?>
		</div>
	</div><! --/row -->
</div><! --/container -->
	<!-- Dynamic Modal -->
	<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="remoteModalLabel"
	     aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
					<div class="modal-header">
						<!--<button type="button" class="close" data-dismiss="modal">&times;</button>-->
						<!--<h4 class="modal-title">Modal Header</h4>-->
					</div>
					<div class="modal-body">
						<p id="alert_fail">Are you sure you want to delete this academic period?</p>
						<p class="alert alert-warning">Warning! This will also delete all courses as well as all files under this academic period</p>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">No</button>

						<button type="submit" class="btn btn-primary" id="year_delete">
							<input type="hidden" id="academicperiod_id_hidden" name="academicperiod_id_hidden">
							<input type="hidden" id="group_id_hidden" name="group_id_hidden">
							Yes
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	<!-- /.modal -->
<?php //include ('footer.php') ?>

<script>
	function confirmDeletePeriod(click_id) {
		//alert($(click_id).children('.period_del').val());
		//alert($(click_id).children('.group_del').val());
		var academicperiod_id= $(click_id).children('.period_del').val();
		var group_id= $(click_id).children('.group_del').val();
		$('#academicperiod_id_hidden').val(academicperiod_id);
		$('#group_id_hidden').val(group_id);

	}

</script>
</body>
</html>
