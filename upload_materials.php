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
//$_SESSION['membertype']= 'admin';
$error= false;
$success= false;
$error_msg_db= "";
$error_msg_period= "";
$error_msg_coursecode= "";
$error_msg_folder= "";
$error_msg_uploadfile= "";
$success_msg= '';
$del_msg= '';
$academic_list= '';
$upload_values= '';
$uploadOk= true;
ini_set('upload_max_filesize', '30M');
//form is submitted
if(isset($_POST['submit']) && $_POST['submit'] == 'submit_upload') {
	if($_POST['academicperiodid'] == "") {
		$error= true;
		$error_msg_period= '<div class="help-block" style="color: red; background-color: white;">Please select academic year</div>';
	}
	if($_POST['course_code_id'] == "") {
		$error= true;
		$error_msg_coursecode= '<div class="help-block" style="color: red; background-color: white;">Please select course code</div>';
	}
	if($_POST['folder'] == "") {
		$error= true;
		$error_msg_folder= '<div class="help-block" style="color: red; background-color: white;">Please select a folder</div>';
	}
	if($_FILES["upload_file"]["name"] == "") {
		$error= true;
		$error_msg_uploadfile= '<div class="help-block" style="color: red; background-color: white;">Please choose a file to upload</div>';
	}

	if(!$error) {//all inputs validated
		$_POST['course_code_id']= validateInput($_POST['course_code_id']);
		$_POST['folder']= validateInput($_POST['folder']);
//		echo '<br><br><br><br><br>'.$_POST['folder']. '======'. $_POST['course_code_id'];
		//retrieve the directory where file will be saved
		$target_dir= getFolderDirectory($conn, $_POST['course_code_id'], $_POST['folder']);
		if($target_dir == '') {
			$uploadOk= false;
			$error_msg_db= '<div class="help-block" style="color: red; background-color: white;">Sorry an error occurred while retrieving directory</div>';
		}
		// Check if file already exists
		$target_file = $target_dir . basename($_FILES["upload_file"]["name"]);
//		echo 'tmp_name: '.$_FILES["upload_file"]["tmp_name"]. '<br>';
//		echo 'target file' . $target_file . '<br>';
//		echo 'basename: '. basename($_FILES["upload_file"]["name"]) . '<br>';
//		echo 'filename: '. $_FILES["upload_file"]["name"];
		if (file_exists($target_file)) {
			$uploadOk= false;
			$error_msg_uploadfile= '<div class="help-block" style="color: red; background-color: white;">Sorry, a file with this name already exists. Please change the filename to upload</div>';
			//echo $error_msg_uploadfile;
		}
		else {

			//echo $_FILES["upload_file"]["tmp_name"];
			//check if file is audio
			$check= check_file_is_audio($_FILES["upload_file"]["tmp_name"]);
			//$check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
			if($check !== false) {
				// Check file size
				if ($_FILES["upload_file"]["size"] > 30000000) {//file is larger than 30mb
					$uploadOk= false;
					$error_msg_folder= '<div class="help-block" style="color: red; background-color: white;">Sorry, the audio you are trying to upload is larger than 30mb</div>';
				}
			}
			else {
				if ($_FILES["upload_file"]["size"] > 5000000) { //file is larger than 5mb
					$uploadOk= false;
					$error_msg_folder= '<div class="help-block" style="color: red; background-color: white;">Sorry, you cannot upload a file larger than 5mb</div>';
				}
			}
		}

		if($uploadOk) { //all file upload validation passed
			// ensure a safe filename
			$name = preg_replace("/[^A-Z0-9._-]/i", "_", $_FILES["upload_file"]["name"]);
			// don't overwrite an existing file
			$i = 0;
			$parts = pathinfo($name);
			while (file_exists($target_file)) {
				$i++;
				$name = $parts["filename"] . "-" . $i . "." . $parts["extension"];
			}
//			echo $_FILES["upload_file"]["tmp_name"]. '<br>';
//			echo $target_file;
			if (move_uploaded_file($_FILES["upload_file"]["tmp_name"], $target_file)) {

				$_POST['who']= $_SESSION['membertype'];
				$_POST['filename']= $_FILES["upload_file"]["name"];
				$_POST['datecreated']= date('Y-m-d');

				if(!$last_inserted= insertRecord($conn, $_POST, 'filemetadata')) {
					$error_msg_db= '<div class="alert alert-danger">An error occurred while uploading file db/div>';
				}
				else {
					$success= true;
					$success_msg='<div class="alert alert-success">The file '. basename( $_FILES["upload_file"]["name"]). ' has been uploaded.</div>';
				}
			} 
			else {
				$error_msg_db= '<div class="alert alert-danger">An error occurred while uploading file '.$_FILES["upload_file"]["error"].'</div>';
			}
		}
	}
	else {
		$upload_values= $_POST;
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
		if ($upload_values != '') {
			if ($academic_period['academicperiodid'] == $upload_values['academicperiodid']) {
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

//build folder options
$select_folder_options= '<option value="">--Select Folder--</option>';
$select_folder_options_arr= array('Assignments'=> 'assignments', 'Lecturer Notes'=> 'lecturernotes', 'Past Questions'=> 'pastquestions', 'Student Notes'=> 'studentnotes', 'Voice Notes'=> 'voicenotes');
foreach($select_folder_options_arr as $option=> $value) {
	if(isset ($_POST['folder'])) {
		if($value == $_POST['folder']) {
			$selected= 'selected';
		}
		else {
			$selected= '';
		}
	}
	else {
		$selected= '';
	}
	$select_folder_options.= '<option value="'.$value.'" '.$selected.'>'.$option.'</option>';
}

$page_title= 'Upload Materials';
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
				<a href="invite_members.php" class="list-group-item">Invite Members</a>
				<a href="upload_materials.php" class="list-group-item active">Upload Materials</a>
				<a href="manage_uploads.php" class="list-group-item">Manage Uploads</a>
			</div>
		</div>
		<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
			<div class="modal-popup" style="color: white;">
				<h3>Upload File</h3><br><br>
				<?php if($success) echo $success_msg; if($error_msg_db != "") echo $error_msg_db;?>
				<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data" class="" >
					<div class="form-group">
						<label for="academicperiodid">Academic Year:</label>
						<select class="form-control" id="academicperiodid" name="academicperiodid" onchange="getCoursesList(this)">
							<?php echo $academic_list; ?>
						</select>
						<?php if($error && $error_msg_period != "") { echo $error_msg_period; } ?>
					</div>
					<div class="form-group">
						<label for="course_code_id">Course:</label>
						<select class="form-control" id="course_code_id" name="course_code_id">

						</select>
						<?php if($error && $error_msg_coursecode != "") { echo $error_msg_coursecode; } ?>
					</div>
					<div class="form-group">
						<label for="folder">Folder:</label>
						<select class="form-control" id="folder" name="folder">
							<?php echo $select_folder_options;?>
						</select>
						<?php if($error && $error_msg_folder != "") { echo $error_msg_folder; } ?>
					</div>
					<div class="form-group">
						<label for="upload_file">Upload File:</label>
						<input class=" form-control btn" type="file" name="upload_file" id="upload_file">
						<div class="alert alert-warning" style="font-size: 11px; padding:0; margin:0; color: brown;">audio files(mp3, mpeg) should not be more than 30mb. Other file types(images, pdf, doc) should not be more than 5mb</div>
						<?php if($error && $error_msg_uploadfile != "") { echo $error_msg_uploadfile; } ?>
					</div>
					<input type="hidden" name="submit" id="submit" value="submit_upload">
					<button type="submit" class="btn btn-submit">Upload</button>
				</form>
			</div>
		</div>
	</div><! --/row -->
</div><! --/container -->

<!-- *****************************************************************************************************************
 FOOTER
 ***************************************************************************************************************** -->
<?php include('footer.php');?>
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
			data: {'academicperiodid':academicperiodid, 'page': 'upload_files'},
			dataType: 'json',
			encode: true,
			success: function (response) {
				$('#course_code_id').empty().append(response.courselist);
			}
		});
	}
</script>

</body>
</html>
