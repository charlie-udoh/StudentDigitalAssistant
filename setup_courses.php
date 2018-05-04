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
$error_msg_period= "";
$error_msg_coursecode= "";
$error_msg_coursename= "";
$error_msg_courseshortname= "";
$error_msg_lecturer= "";
$del_msg= '';
$edit_msg= '';
$academic_list= '';
$course_values= '';

//form is submitted
if(isset($_POST['submit']) && $_POST['submit'] == 'submit_course') {
	if(isset($_POST['academicperiodid']) && $_POST['academicperiodid'] == "") {
		$error= true;
		$error_msg_period= '<div class="help-block" style="color: red; background-color: white;">Please select academic year</div>';
	}
	if($_POST['coursecode'] == "") {
		$error= true;
		$error_msg_coursecode= '<div class="help-block" style="color: red; background-color: white;">Please enter the course code</div>';
	}
	if($_POST['coursename'] == "") {
		$error= true;
		$error_msg_coursename= '<div class="help-block" style="color: red; background-color: white;">Please enter the course name</div>';
	}
	if($_POST['lecturer'] == "") {
		$error= true;
		$error_msg_lecturer= '<div class="help-block" style="color: red; background-color: white;">Please enter the lecturer\'s name</div>';
	}

	if(!$error){
		if(isset($_POST['academicperiodid'])) {
			$_POST['academicperiodid']= validateInput($_POST['academicperiodid']);
		}
		if(isset($_POST['academicperiodidhidden'])) {
			$_POST['academicperiodidhidden']= validateInput($_POST['academicperiodidhidden']);
		}

		$_POST['courseid']= validateInput($_POST['courseid']);
		$_POST['coursecode']= validateInput($_POST['coursecode']);
		$_POST['coursename']= validateInput($_POST['coursename']);
		$_POST['lecturer']= validateInput($_POST['lecturer']);

		if(!isset($_POST['academicperiodid'])) {
			$_POST['period_id']= $_POST['academicperiodidhidden'];
		}
		else {
			$_POST['period_id']=$_POST['academicperiodid'];
		}
		if (isset($_POST['courseid']) && $_POST['courseid'] != "") {
			if(!$last_inserted= updateRecord($conn, $_POST, 'course')) {
				$error= true;
				$error_msg_db= '<div class="alert alert-danger">An error occurred while updating record</div>';
			}
			else {
				$success= true;
				$success_msg= '<div class="alert alert-success">Course has been edited successfully</div>';
			}
		}
		else {
			if(checkRecordExists($conn, $_POST, 'course')) {
				$error= true;
				$error_msg_db= '<div class="alert alert-danger">The course you have entered already exists</div>';
			}
			else {
				if(!$last_inserted= insertRecord($conn, $_POST, 'course')) {
					$error= true;
					$error_msg_db= '<div class="alert alert-danger">An error occurred while inserting record</div>';
				}
				else {
					$course_record= getCourses($conn,0, $last_inserted);
					
					$group_dir = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR. $_SESSION['groupid']. DIRECTORY_SEPARATOR;
					$course_dir= DIRECTORY_SEPARATOR.$course_record['coursecode']. DIRECTORY_SEPARATOR;
					$assignments_dir= 'assignments'.DIRECTORY_SEPARATOR;
					$lecturernotes_dir= 'lecturernotes'.DIRECTORY_SEPARATOR;
					$pastquestions_dir= 'pastquestions'.DIRECTORY_SEPARATOR;
					$studentnotes_dir= 'studentnotes'.DIRECTORY_SEPARATOR;
					$voicenotes_dir= 'voicenotes'.DIRECTORY_SEPARATOR;
					$academic_period_dir_arr= glob($group_dir.'*', GLOB_ONLYDIR);

					foreach ($academic_period_dir_arr as $dir) {
						$dir_name= basename($dir);
						if($dir_name == $course_record['academicperiodid']) {
							if(!file_exists($dir.$course_dir)) {
								//create course directory in period directory
								@mkdir($dir.$course_dir);
								//create upload directories in course directory
								@mkdir($dir.$course_dir.$assignments_dir);
								@mkdir($dir.$course_dir.$lecturernotes_dir);
								@mkdir($dir.$course_dir.$pastquestions_dir);
								@mkdir($dir.$course_dir.$studentnotes_dir);
								@mkdir($dir.$course_dir.$voicenotes_dir);
							}
						}
					}
					$success= true;
					$success_msg= '<div class="alert alert-success">Course has been created successfully</div>';
				}
			}
		}
	}

	if($error) {
		$course_values= $_POST;
	}
}

//course is deleted
if( isset($_POST['course_id_hidden']) && isset($_POST['academicperiod_id_hidden'])) {
	$course_deleted= getCourses($conn, 0, $_POST['course_id_hidden']);
	$dir = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR.$_SESSION['groupid'].DIRECTORY_SEPARATOR.$course_deleted['academicperiodid'].DIRECTORY_SEPARATOR.$course_deleted['coursecode'];
	if(! deleteDirectory($dir)) {
		$del_msg= '<p class="alert alert-danger">An error occurred while deleting <b>
'.$course_deleted['coursecode'].'</b></p>';
	}
	else {
		if(deleteRecord($conn, $_POST['course_id_hidden'], 'course', '', 'filemetadata')) {
			if (!deleteRecord($conn, $_POST['course_id_hidden'], 'course')) {
				$del_msg = '<p class="alert alert-danger">An error occurred while deleting <b>' . $course_deleted['coursecode'] . '</b></p>';
			} else {
				$del_msg = '<p class="alert alert-success"><b>' . $course_deleted['coursecode'] . '</b> deleted successfully</p>';
			}
		}
		else {
			$del_msg = '<p class="alert alert-danger">An error occurred while deleting <b>' . $course_deleted['coursecode'] . '</b></p>';
		}
	}
}

//get user details
$user_details= getUserDetails($conn, $_SESSION['groupid']);

//get academic period list for select element in form
if(!$academic_periods= getAcademicYear($conn, $_SESSION['groupid'])) {
	$academic_list= '<option value="">No available Academic Periods</option>';
}
else {
	foreach ($academic_periods as $academic_period) {
		if ($course_values != '') {
			if ($academic_period['academicperiodid'] == $course_values['academicperiodid']) {
				$selected = 'selected';
			} else {
				$selected = '';
			}
		} else {
			$selected = '';
		}
		$academic_list .= '<option value="' . $academic_period['academicperiodid'] . '" ' . $selected . '>' . $academic_period['academicperiod'] . '</option>';
	}
}

$page_title= 'Setup Courses';
include('header_a.php');
?>



<!-- *****************************************************************************************************************
 AGENCY ABOUT
 ***************************************************************************************************************** -->

<div class="container mtb">
	<div class="row">
		<div class="col-lg-3">
			<?php include ("groupdetails.php");?>
			<br>			
			<h4>What would you like to do?</h4>
			<div class="list-group">
				<a href="setup_academic_year.php" class="list-group-item ">Setup Academic Year</a>
				<a href="setup_courses.php" class="list-group-item active">Setup Courses</a>
				<a href="invite_members.php" class="list-group-item">Invite Members</a>
				<a href="upload_materials.php" class="list-group-item ">Upload Materials</a>
				<a href="manage_uploads.php" class="list-group-item">Manage Uploads</a>
			</div>
		</div>
		<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
			<div class="modal-popup" style="color: white;">
				<h3>Setup Courses</h3>
				<?php if($success) echo $success_msg; if($error_msg_db != "") echo $error_msg_db; if($edit_msg != "") echo $edit_msg; ?>
				<div id="edit_display"></div>
				<form action="<?php htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
					<div class="form-group">
						<label for="academicperiodid">Academic Period:</label>
						<select class="form-control" id="academicperiodid" name="academicperiodid" onchange="getCoursesList(this)">
							<?php echo $academic_list; ?>
						</select>
						<?php if($error && $error_msg_period != "") { echo $error_msg_period; } ?>
					</div>
					<div class="form-group">
						<label for="coursecode">Course Code:</label>
						<input type="text" class="form-control" id="coursecode" name="coursecode" value="<?php if($course_values != "") echo $course_values['coursecode']; ?>">
						<?php if($error && $error_msg_coursecode != "") { echo $error_msg_coursecode; } ?>
					</div>
					<div class="form-group">
						<label for="coursename">Course Name:</label>
						<input type="text" class="form-control" id="coursename" name="coursename" value="<?php if($course_values != "") echo $course_values['coursename']; ?>">
						<?php if($error && $error_msg_coursename != "") { echo $error_msg_coursename; } ?>
					</div>
					<div class="form-group">
						<label for="courseshortname">Course Short Name(optional):</label>
						<input type="text" class="form-control" id="courseshortname" name="courseshortname" value="<?php if($course_values != "") echo $course_values['courseshortname']; ?>">
					</div>
					<div class="form-group">
						<label for="lecturer">Lecturer:</label>
						<input type="text" class="form-control" id="lecturer" name="lecturer" value="<?php if($course_values != "") echo $course_values['lecturer']; ?>">
						<?php if($error && $error_msg_lecturer != "") { echo $error_msg_lecturer; } ?>
					</div>
					<input type="hidden" name="groupid" id="groupid" value="<?php echo $_SESSION['groupid']; ?>">
					<input type="hidden" name="courseid" id="courseid" value="<?php if (isset($_GET['courseid'])) echo $_GET['courseid']; ?>">
					<input type="hidden" name="academicperiodidhidden" id="academicperiodidhidden" value="">
					<input type="hidden" value="submit_course" name="submit" id="submit">
					<button type="submit" class="btn btn-submit">Submit</button>
				</form>
			</div>
		</div>
		<div class="col-lg-3">
			<?php echo $del_msg; ?>
			<div id="courselist_container">

			</div>
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
						<p id="alert_fail">Are you sure you want to delete this course?</p>
						<p class="alert alert-warning">Warning! This will also delete all files saved for this course</p>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">No</button>

						<button type="submit" class="btn btn-primary" id="course_delete">
							<input type="hidden" id="course_id_hidden" name="course_id_hidden">
							<input type="hidden" id="academicperiod_id_hidden" name="academicperiod_id_hidden">
							Yes
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	<!-- /.modal -->
<!-- *****************************************************************************************************************
 FOOTER
 ***************************************************************************************************************** -->
<?php include('footer.php'); ?>

<script>
	$(document).ready(function () {
		var element= document.getElementById('academicperiodid');
		getCoursesList(element);
	});

	function getCoursesList(element) {
		var academicperiodid= element.value;
		//alert (academicperiodid);
		$.ajax({
			type: 'POST',
			url: 'ajax_get_courses.php',
			data: {'academicperiodid':academicperiodid, 'page': 'setup_courses'},
			dataType: 'json',
			encode: true,
			success: function (response) {
				$('#courselist_container').empty().append(response.courselist);
			}
		});
	}

	function confirmDeleteCourse(click_id) {
		//alert($(click_id).children('.period_del').val());
		var course_id= $(click_id).children('.course_del').val();
		$('#course_id_hidden').val(course_id);

	}

	function editCourse(click_id) {
		$('div.alert').remove();
		var course_id= $(click_id).children('.course_edit').val();
		$.ajax({
			type: 'POST',
			url: 'ajax_get_courses.php',
			data: {'courseid':course_id},
			dataType: 'json',
			encode: true,
			success: function (response) {
				$('#courseid').val(response.courseid);
				$('#coursecode').val(response.coursecode).prop("readonly", true);
				$('#coursename').val(response.coursename);
				$('#courseshortname').val(response.courseshortname);
				$('#lecturer').val(response.lecturer);
				$('#academicperiodid').val(response.academicperiodid).prop("disabled", true);
				$('#academicperiodidhidden').val(response.academicperiodid);
				$('#edit_display').html('<div class="help-block" style="color: red; background-color: white;">You are now editing <b>'+response.coursecode+ '</b>...</div>');
			}
		});
	}
</script>

</body>
</html>
