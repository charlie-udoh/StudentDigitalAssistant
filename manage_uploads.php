<?php
require 'include/util.php';
include ('include/functions.php');
include ('include/common.php');
include ('check_user_log.php');
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

if (!$conn) {
	die("Connection failed: " . mysqli_connect_error());
}
$del_msg= '';

//get user details
$user_details= getUserDetails($conn, $_SESSION['groupid']);

if(!($academicperiod= getAcademicYear($conn, $_SESSION['groupid']))) {
	$latest_academicperiod= '';

}
else {
	$latest_academicperiod= $academicperiod[0]['academicperiodid'];
}


if(!($courses= getCourses($conn, $latest_academicperiod))) {
	$latest_course= '';
	$latest_course_code= '';
}
else {
	$latest_course= $courses[0]['courseid'];
	$latest_course_code= $courses[0]['coursecode'];
}


mysqli_close($conn);
//echo $academicperiodlatest;
$page_title= 'Manage Uploads';
include('header_a.php');
?>


<!-- *****************************************************************************************************************
 AGENCY ABOUT
 ***************************************************************************************************************** -->

<div class="container mtb">
	<div class="row">
		<div class="col-lg-12">
				<h3><?php echo $user_details['groupname'];?></h3>
				<p><b><?php echo $user_details['institution']."</b> &nbsp;| &nbsp;".
				$user_details['faculty'].
				" &nbsp;| &nbsp;".$user_details['department']." &nbsp;| &nbsp;".$user_details['programme'].
				"(".$user_details['courseofstudy'].
				")";?>
				</p>
		</div>
		<div class="col-lg-3">
			<div class="well" id="academicperiod_content" style="height: 400px; overflow-y: scroll;">

			</div>
		</div>
		<div class="col-lg-3">
			<div class="well" id="course_content" style="height: 400px; overflow-y: scroll;">

			</div>
		</div>
		<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12" >
			<div id="display_msg"></div>

			<div class="well" style="height: 400px; overflow-y: scroll;">
				<div class="panel-group" id="accordion" >
					<!-- LECTURE NOTES -->
					<div class="panel panel-default" id="lecture_notes_panel">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a data-toggle="collapse" data-parent="#accordion" href="#collapse_lecturenotes">
									Lecture Notes</a>
							</h4>
						</div>
						<div id="collapse_lecturenotes" class="panel-collapse collapse in">

						</div>
					</div>
					<!-- VOICE NOTES -->
					<div class="panel panel-default" id="voice_notes_panel">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a data-toggle="collapse" data-parent="#accordion" href="#collapse_voicenotes">
									Voice Notes</a>
							</h4>
						</div>
						<div id="collapse_voicenotes" class="panel-collapse collapse">

						</div>
					</div>
					<!-- ASSIGNMENTS -->
					<div class="panel panel-default" id="assignment_panel" >
						<div class="panel-heading">
							<h4 class="panel-title">
								<a data-toggle="collapse" data-parent="#accordion" href="#collapse_assignments">
									Assignments</a>
							</h4>
						</div>
						<div id="collapse_assignments" class="panel-collapse collapse">

						</div>
					</div>
					<!-- PAST QUESTIONS -->
					<div class="panel panel-default" id="past_questions_panel">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a data-toggle="collapse" data-parent="#accordion" href="#collapse_pastquestions">
									Past Questions</a>
							</h4>
						</div>
						<div id="collapse_pastquestions" class="panel-collapse collapse">

						</div>
					</div>
					<!-- STUDENT NOTES-->
					<div class="panel panel-default" id="images_panel">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a data-toggle="collapse" data-parent="#accordion" href="#collapse_studentnotes">
									Student Notes</a>
							</h4>
						</div>
						<div id="collapse_studentnotes" class="panel-collapse collapse">

						</div>
					</div>
				</div>
			</div>


		</div>
		<?php if(strtolower($_SESSION['membertype']) == 'admin') { ?>
		<div class="col-lg-12">
			<div class="list-group">
				<ul class="nav nav-pills nav-justified">
					<li><a href="setup_academic_year.php" class="list-group-item ">Setup Academic Year</a></li>
					<li><a href="setup_courses.php" class="list-group-item ">Setup Courses</a></li>
					<li><a href="invite_members.php" class="list-group-item">Invite Members</a></li>
					<li><a href="upload_materials.php" class="list-group-item">Upload Materials</a></li>
					<li><a href="manage_uploads.php" class="list-group-item active" >Manage Uploads</a></li>
				</ul>
			</div>
		</div>
		<?php } ?>
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
					<p id="alert_fail">Are you sure you want to delete this file?</p>
					<p class="alert alert-warning">Warning! You cannot recover this file back once it's deleted!</p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">No</button>
					
					<button type="button" class="btn btn-primary" id="file_delete_button">
						<input type="hidden" id="file_path_hidden" name="file_path_hidden">
						<input type="hidden" id="courseid_del_hidden" name="courseid_del_hidden">
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
<?php include ('footer.php'); ?>

<script>
	$(document).ready(function () {
		var period_id= '<?php echo $latest_academicperiod; ?>';
		var course_id= '<?php echo $latest_course; ?>';
		var course_code= '<?php echo $latest_course_code; ?>';
//		alert(period_id);
//		alert(course_id);
		getUploadContent(period_id, course_id, course_code);
	});
	
	function getUploadContent(period_id, course_id, course_code) {
		$.blockUI({
			message: '<h1 style=""><img style="" width="100" height="100" src="assets/img/ajax_loader_blue.gif" />Loading...</h1>',
			css: {backgroundColor: '#grey', opacity: 0.3, color: '#fff', border: '0px solid #C1DAD7'}
		});
		$.ajax({
			type: 'POST',
			url: 'ajax_manage_uploads.php',
			data: {
				'academicperiodid':period_id,
				'courseid': course_id,
				'coursecode': course_code,
				'action': 'get'
			},
			dataType: 'json',
			encode: true,
			success: function (response) {
				$.unblockUI();
				$('#academicperiod_content').empty().append(response.period_content);
				$('#course_content').empty().append(response.course_content);
				$('#collapse_assignments').empty().append(response.assignments);
				$('#collapse_voicenotes').empty().append(response.voicenotes);
				$('#collapse_studentnotes').empty().append(response.studentnotes);
				$('#collapse_lecturenotes').empty().append(response.lecturernotes);
				$('#collapse_pastquestions').empty().append(response.pastquestions);
			}
		});
	}
	
	function getPostData(element) {
		var period_id;
		var course_id;
		var course_code;
		if(element.name == 'period_item') {
			period_id= $(element).children('.academic_period_hidden').val();
			course_id= '0';
			course_code= '';
		}
		else if(element.name == 'course_item') {
			period_id= $(element).children('.coursecode_period_hidden').val();
			course_id= $(element).children('.courseid_hidden').val();
			course_code= $(element).children('.coursecode_hidden').val();
		}
		getUploadContent(period_id, course_id, course_code);
	}

	function confirmDeleteFile(click_id) {
		//alert($(click_id).children('.period_del').val());
		var file_path= $(click_id).children('.file_delete').val();
		var courseid= $(click_id).children('.courseid_del').val();
		$('#file_path_hidden').val(file_path);
		$('#courseid_del_hidden').val(courseid);
	}

	$('#file_delete_button').click(function() {
		$.ajax({
			type: 'POST',
			url: 'ajax_manage_uploads.php',
			data: {
				'file_path_hidden':$('#file_path_hidden').val(),
				'courseid_del_hidden': $('#courseid_del_hidden').val(),
				'action': 'delete'
			},
			dataType: 'json',
			encode: true,
			success: function (response) {
				$('#display_msg').empty().append(response.message).slideDown('2000').slideUp('slow');
				$('#collapse_assignments').empty().append(response.assignments);
				$('#collapse_voicenotes').empty().append(response.voicenotes);
				$('#collapse_studentnotes').empty().append(response.studentnotes);
				$('#collapse_lecturenotes').empty().append(response.lecturernotes);
				$('#collapse_pastquestions').empty().append(response.pastquestions);
				$('#deleteModal').modal('hide');
			}
		});
	})
</script>

</body>
</html>
