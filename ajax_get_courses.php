<?php

require 'include/util.php';
include ('include/functions.php');

$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

if (!$conn) {
	die("Connection failed: " . mysqli_connect_error());
}
//$_SESSION['groupid']= 1;
$data= array();

if(isset($_POST['page']) && $_POST['page'] == 'upload_files') {
	$course_list= "";
	$academicperiod= getAcademicYear($conn, 0, $_POST['academicperiodid']);
	if(!$courses= getCourses($conn, $_POST['academicperiodid'])) {
		$course_list.= '<option value="">You have not set up any courses for this academic period</option>';
	}
	else {
		foreach ($courses as $course) {
			$course_list.= '<option value="'.$course['courseid'].'">'.$course['coursecode'].'</option>';
		}
	}
	$data['courselist']= $course_list;
	$data['page']= $_POST['page'];
	echo json_encode($data);
}

if(isset($_POST['page']) && $_POST['page'] == 'setup_courses') {
	$academicperiod= getAcademicYear($conn, 0, $_POST['academicperiodid']);
	$display_academicperiod= $academicperiod['academicperiod'];
	$course_list= '<b>Manage Courses For '.$display_academicperiod.'</b>';

	if(!$courses= getCourses($conn, $_POST['academicperiodid'])) {
		$course_list.= '<p>You have not set up any courses for this academic period</p>';
	}
	else {
		$course_list.= '<table class="table table-hovered table-striped">
				<thead>
					<tr>
						<th>Course Code</th>
						<th>Edit|Delete</th>
					</tr>
				</thead>
				<tbody>';
		foreach ($courses as $course) {
			$course_list.= '<tr>
					    <td>' . $course['coursecode'] . '</td>
						<td>
						<button class="btn btn-sm btn-primary button-manage" title="edit course_details" onclick=\'editCourse(this)\'>
						<input type="hidden" name="courseid_edit" class="course_edit" id="courseid_edit" value="' . $course['courseid'] . '">
							<i class="fa fa-pencil"></i>
						</button>
							<button class="btn btn-sm btn-danger button-manage" type=\'button\'  title=\'Delete\' data-toggle=\'modal\' data-target=\'#deleteModal\' data-original-title=\'Delete\' onclick=\'confirmDeleteCourse(this)\'>
								<input type="hidden" name="courseid_del" class="course_del" id="courseid_del" value="' . $course['courseid'] . '">
								<i class="fa fa-times-circle" ></i>
							</button>
						</td>
					</tr>';
		}
		$course_list.='</tbody></table>';
	}
	$data['courselist']= $course_list;
	$data['page']= $_POST['page'];
	echo json_encode($data);
}

if(isset($_POST['courseid'])) {
	if($courses= getCourses($conn, 0, $_POST['courseid'])) {
		$data['courseid']= $courses['courseid'];
		$data['coursecode']= $courses['coursecode'];
		$data['coursename']= $courses['coursename'];
		$data['courseshortname']= $courses['courseshortname'];
		$data['lecturer']= $courses['lecturer'];
		$data['academicperiodid']= $courses['academicperiodid'];
		$data['single_course']= true;
	}
	echo json_encode($data);
}