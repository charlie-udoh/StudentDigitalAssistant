<?php
require 'include/util.php';
include ('include/functions.php');

$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

if (!$conn) {
	die("Connection failed: " . mysqli_connect_error());
}
$data= array();


if(isset($_POST['action']) && $_POST['action'] == 'get') {
	$academic_period_container= "<div class='list-group'>";
	$academic_period_header= "<b>Your academic Periods</b>";
	$academic_period_content= "";
	$course_container= "<div class='list-group'>";
	$course_header= "<b>Courses under";
	$course_content= "";
	$period_name= "";
	$active= "";
	if(strtolower($_SESSION['membertype']) == 'admin') {
		$msg_period= '<h4>Your Academic Periods</h4>No info to display. You need to set up an academic period';
		$msg_course= '<h4>Your Courses</h4>No Courses to display. You need to set up a course under a period';
		$msg_file= 'No Files to display. You need to upload a file for a course';
	}
	else {
		$msg_period= 'No info to display';
		$msg_course= 'No info to display';
		$msg_file= 'No info to display';
	}


	//check if valid id was posted
	if($_POST['academicperiodid'] != 0 || $_POST['academicperiodid'] != '') {
		$continue_academic= true;

		if($_POST['courseid'] == '0') {
			if(!($courses= getCourses($conn, $_POST['academicperiodid'])) ) {
				$continue_course= false;
			}
			else {
				$courseid= $courses[0]['courseid'];
				$coursecode= $courses[0]['coursecode'];
				$continue_course= true;
//				echo 'continue course [0] is true';
			}
		}
		else {
			if(trim($_POST['courseid']) != '' && trim($_POST['coursecode']) != '') {
				$courseid= $_POST['courseid'];
				$coursecode= $_POST['coursecode'];
				$continue_course= true;

			}
			else {
				$continue_course = false;

			}
		}
	}
	else {
		$continue_academic= false;
	}


	//get the academic periods for the group and build the display string

	if($continue_academic) {
		//get the academic periods for the group and build the display string
		$academicperiod= getAcademicYear($conn, $_SESSION['groupid']);
		foreach($academicperiod as $period) {
			$active= "";
			if($period['academicperiodid'] == $_POST['academicperiodid']) {
				$active= 'active';
				$period_name= $period['academicperiod'];
			}
			$academic_period_content.=  "<a name='period_item' href='#' class='list-group-item $active' onclick='getPostData(this); return false;'>".$period['academicperiod']."<input type='hidden' class='academic_period_hidden' value='".$period['academicperiodid']."'></a>";
		}

		if($continue_course) {
			//get the courses for the period and build the display string
			$courses= getCourses($conn, $_POST['academicperiodid']);
			foreach($courses as $course) {
				$active= "";
				if($course['courseid'] == $courseid) {
					$active= 'active';
					$course_code= $course['coursecode'];
				}
				$course_content.=  "<a name='course_item' href='#' class='list-group-item $active' onclick='getPostData(this); return false;'>".$course['coursecode']."<input type='hidden' class='coursecode_period_hidden' value='".$_POST['academicperiodid']."'><input type='hidden' class='coursecode_hidden' value='".$course['coursecode']."'><input type='hidden' class='courseid_hidden' value='".$course['courseid']."'></a>";
			}

			$target_dir_lecturernotes= getFolderDirectory($conn, $courseid, 'lecturernotes');
			$target_dir_assignments= getFolderDirectory($conn, $courseid, 'assignments');
			$target_dir_pastquestions= getFolderDirectory($conn, $courseid, 'pastquestions');
			$target_dir_studentnotes= getFolderDirectory($conn, $courseid, 'studentnotes');
			$target_dir_voicenotes= getFolderDirectory($conn, $courseid, 'voicenotes');

			$dir_arr= array('lecturernotes'=> $target_dir_lecturernotes,
			                'assignments'=> $target_dir_assignments,
			                'pastquestions'=> $target_dir_pastquestions,
			                'studentnotes'=> $target_dir_studentnotes ,
			                'voicenotes' => $target_dir_voicenotes
			);
			//	print_r($dir_arr);
			$dir_arr= array_filter($dir_arr);
			foreach ($dir_arr as $folder => $dir) {
				if(!empty($dir) || $dir == '') {
					$file_list= "<table class=\"table table-hovered table-striped\"><thead><tr><th colspan=\"2\">File</th></tr></thead><tbody>";
					$files = glob($dir.'*');

					if(!empty($files)) {
						foreach ($files as $file) {
							$target_file= $file;
							$path_to_file= APP_URL. '/uploads/'. getPath($target_file);

							if(strtolower($_SESSION['membertype']) == 'admin') {
								$delete_button= "<button class=\"btn btn-sm btn-danger button-manage\" type='button'  title='Delete' data-toggle='modal' data-target='#deleteModal' data-original-title='Delete' onclick='confirmDeleteFile(this)'>
								<input type='hidden' id='file_path' class='file_delete' value='$target_file'>
								 <input type='hidden' id='courseid_del' class='courseid_del' value='$courseid'>
									<i class=\"fa fa-times-circle\" ></i>
								</button>";
							}
							else {
								$delete_button= "";
							}
							$file_name= basename($file);

							$file_list.= "<tr>
							<td><a href='$path_to_file' target='_blank'>$file_name</a></td>
							<td>"
								//								<button class=\"btn btn-sm btn-success button-manage\" title=\"view file\">
								//								<input type='hidden' id='file_path' class='file_listen' value='$path_to_file'>
								//									<i class=\"fa fa-file\" ></i>
								//								</button>
								.$delete_button."
							</td>
						</tr>";
						}
					}
					else {
						$file_list.= "<tr><td colspan='2'>No files to View</td></tr>";
					}

					$file_list.= '</tbody></table>';

				}
				else {
					$file_list.= "<tr><td colspan='2'>No files to View</td></tr>";
				}
				if($folder == 'lecturernotes') {
					$data['lecturernotes']= $file_list;
				}
				elseif($folder == 'assignments') {
					$data['assignments']= $file_list;
				}
				elseif($folder == 'pastquestions') {
					$data['pastquestions']= $file_list;
				}
				elseif ($folder == 'studentnotes') {
					$data['studentnotes']= $file_list;
				}
				elseif($folder == 'voicenotes') {
					$data['voicenotes']= $file_list;
				}
			}

		}
		else {
			$data['assignments']= $msg_file;
			$data['pastquestions']= $msg_file;
			$data['studentnotes']= $msg_file;
			$data['voicenotes']= $msg_file;
			$data['lecturernotes']= $msg_file;
			$data['course_content']= $msg_file;
		}
		//	echo $target_dir_pastquestions;
		if($academic_period_content != "") {
			$academic_period_container.= $academic_period_header.$academic_period_content.'</div>';
		}
		else {
			$academic_period_container.= '<b>There are no academic periods set up yet</b></div>';
		}
		if($course_content != "") {
			$course_container.= $course_header." ".$period_name."</b>".$course_content. "  </div>";
		}
		else {
			if($academic_period_content == ''){
				$course_container.= "<b>No courses to view</b></div>";
			}
			else {
				$course_container.= "<b>No courses set up yet for ".$period_name." </b></div>";
			}
		}

		$data['period_content']= $academic_period_container;
		$data['course_content']= $course_container;
	}
	else {
		$data['assignments']= $msg_file;
		$data['pastquestions']= $msg_file;
		$data['studentnotes']= $msg_file;
		$data['voicenotes']= $msg_file;
		$data['lecturernotes']= $msg_file;
		$data['course_content']= $msg_course;
		$data['period_content']= $msg_period;
	}
	echo json_encode($data);
}

//course is deleted
if(isset($_POST['action']) && $_POST['action'] == 'delete') {
	$file_path= $_POST['file_path_hidden'];
	$courseid_del= $_POST['courseid_del_hidden'];
	$file_name= basename($file_path);
	
	$file_to_be_deleted= getFileDetails($conn, $file_name, $courseid_del);
	
	if(! unlink($file_path)) {
		$del_msg= '<p class="alert alert-danger">An error occurred while deleting <b>
'.$course_deleted['coursecode'].'</b></p>';
	}
	else {
		if (! deleteRecord($conn, $courseid_del, 'filemetadata', $file_name)) {
			$del_msg= '<p class="alert alert-danger">An error occurred while deleting <b>
'.$course_deleted['coursecode'].'</b></p>';
		}
		else{
			$target_dir_lecturernotes= getFolderDirectory($conn, $courseid_del, 'lecturernotes');
			$target_dir_assignments= getFolderDirectory($conn, $courseid_del, 'assignments');
			$target_dir_pastquestions= getFolderDirectory($conn, $courseid_del, 'pastquestions');
			$target_dir_studentnotes= getFolderDirectory($conn, $courseid_del, 'studentnotes');
			$target_dir_voicenotes= getFolderDirectory($conn, $courseid_del, 'voicenotes');

			$dir_arr= array('lecturernotes'=> $target_dir_lecturernotes,
			                'assignments'=> $target_dir_assignments,
			                'pastquestions'=> $target_dir_pastquestions,
			                'studentnotes'=> $target_dir_studentnotes ,
			                'voicenotes' => $target_dir_voicenotes
			);

			//	print_r($dir_arr);
			foreach ($dir_arr as $folder => $dir) {
				$file_list= "<table class=\"table table-hovered table-striped\"><thead><tr><th colspan=\"2\">File</th></tr></thead><tbody>";
				$files = glob($dir.'*');

				if(!empty($files)) {
					foreach ($files as $file) {
						$target_file= $file;

						$file_name= basename($file);

						$file_list.= "<tr>
							<td><a href='$target_file'>$file_name</a></td>
							<td>
								<button class=\"btn btn-sm btn-success button-manage\" title=\"view file\">
								<input type='hidden' id='file_path' class='file_listen' value='$target_file'>
									<i class=\"fa fa-file\" ></i>
								</button>
								<button class=\"btn btn-sm btn-danger button-manage\" type='button'  title='Delete' data-toggle='modal' data-target='#deleteModal' data-original-title='Delete' onclick='confirmDeleteFile(this)'>
								<input type='hidden' id='file_path' class='file_delete' value='$target_file'>
								 <input type='hidden' id='courseid_del' class='courseid_del' value='$courseid_del'>
									<i class=\"fa fa-times-circle\" ></i>
								</button>
							</td>
						</tr>";
					}
				}
				else {
					$file_list.= "<tr><td colspan='2'>No files to View</td></tr>";
				}

				$file_list.= '</tbody></table>';
				if($folder == 'lecturernotes') {
					$data['lecturernotes']= $file_list;
				}
				elseif($folder == 'assignments') {
					$data['assignments']= $file_list;
				}
				elseif($folder == 'pastquestions') {
					$data['pastquestions']= $file_list;
				}
				elseif ($folder == 'studentnotes') {
					$data['studentnotes']= $file_list;
				}
				elseif($folder == 'voicenotes') {
					$data['voicenotes']= $file_list;
				}
			}
			$del_msg= '<p class="alert alert-success"><b>'.$file_to_be_deleted['filename'].'</b> deleted successfully</p>';
		}
	}
	$data['message']= $del_msg;
	echo json_encode($data);
}