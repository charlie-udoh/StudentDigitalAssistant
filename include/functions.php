<?php
include 'util.php';
function getAcademicYear($conn, $group_id=0, $record_id=0) {
	$yearArr= array();
	if ($record_id != 0) {
		$sql = "SELECT * FROM academicperiod WHERE academicperiodid= $record_id";
	}
	else {
		$sql = "SELECT * FROM academicperiod WHERE groupid= $group_id ORDER BY academicperiodid DESC";
	}
	if($result= mysqli_query($conn, $sql)) {
		if(mysqli_num_rows($result) > 0) {
			if ($record_id != 0) {
				return mysqli_fetch_assoc($result);
			}
			else {
				while ($row = mysqli_fetch_assoc($result)) {
					$yearArr[] = $row;
				}
			}
			return $yearArr;
		}
	}
	return false;
}

function getCourses($conn, $academicperiod_id, $record_id=0) {
	$courseArr= array();
	if ($record_id != 0) {
		$sql = "SELECT * FROM course WHERE courseid= $record_id";
	}
	else {
		$sql = "SELECT * FROM course WHERE academicperiodid= $academicperiod_id ORDER BY courseid DESC";
	}
	//echo $sql;
	if($result= mysqli_query($conn, $sql)) {
		if(mysqli_num_rows($result) > 0) {
			if ($record_id != 0) {
				return mysqli_fetch_assoc($result);
			}
			else {
				while ($row = mysqli_fetch_assoc($result)) {
					$courseArr[] = $row;
				}
			}
			
			return $courseArr;
		}
	}
	return false;
}

function getStudentDetails($conn, $group_id=0, $record_id=0, $membertype= '') {
	
}

function getFileDetails($conn, $filename, $courseid) {
	$sql= "SELECT * FROM filemetadata WHERE filename= '$filename' AND courseid= '$courseid'";
	if($result= mysqli_query($conn, $sql)) {
		if(mysqli_num_rows($result) > 0) {
			return mysqli_fetch_assoc($result);
		}
	}
	return false;
}
function getUserDetails($conn, $group_id) {
	$sql= "SELECT * FROM studyprogram WHERE groupid= $group_id";
	if($result= mysqli_query($conn, $sql)) {
		if(mysqli_num_rows($result) > 0) {
			return mysqli_fetch_assoc($result);
		}
	}
	return false;
}

function insertRecord($conn, $data, $table) {
	foreach($data as $key => $input) {
		$input= mysqli_real_escape_string($conn, $input);
		$valid_data[$key]= $input;
	}
	$data= $valid_data;
	switch ($table) {
		case "academicperiod":
			$sql= "INSERT into ".$table." (academicperiod, groupid, datecreated) VALUES ('".$data['academicperiod']."','".$data['groupid']."', '".$data['datecreated']."')";
			break;
		case "course":
			$sql= "INSERT into ".$table." (coursecode, coursename, courseshortname, lecturer, academicperiodid) VALUES ('".$data['coursecode']."', '".$data['coursename']."','".$data['courseshortname']."','".$data['lecturer']."','".$data['period_id']."')";
			break;
		case "filemetadata":
			$sql= "INSERT into ".$table." (filename, courseid, folder, who) VALUES ('".$data['filename']."', '".$data['course_code_id']."','".$data['folder']."','".$data['who']."')"; //','".$data['datecreated']."
			break;
		default:
			return false;
	}
	if($result= mysqli_query($conn, $sql)) {
		$last_inserted_id= mysqli_insert_id($conn);
		return $last_inserted_id;
	}
	return false;
}

function deleteRecord($conn, $id, $table, $filename='', $child_table= '') {
	if($child_table != '') {
		$sql= "DELETE FROM ".$child_table. " WHERE ".$table."id = $id";
	}
	else {
		if($filename != '' && $table == 'filemetadata') {
			$sql= "DELETE FROM ".$table. " WHERE courseid = $id AND filename= '$filename'";
		}
		else {
			$sql= "DELETE FROM ".$table. " WHERE ".$table."id = $id";
		}
	}


	if($result= mysqli_query($conn, $sql)) {
		return true;
	}
	return false;
}

function updateRecord($conn, $data,$table) {
	foreach($data as $key => $input) {
		$input= mysqli_real_escape_string($conn, $input);
		$valid_data[$key]= $input;
	}
	$data= $valid_data;
	switch($table) {
		case "course":
			$sql= "UPDATE ".$table." SET coursecode ='".$data['coursecode']."', coursename='".$data['coursename']."', courseshortname='".$data['courseshortname']."', lecturer='".$data['lecturer']."', academicperiodid='".$data['period_id']."' WHERE ".$table."id= ".$data[$table.'id'];
			break;
		default:
			return false;
	}
	if($result= mysqli_query($conn, $sql)) {
		return true;
	}
	return false;
}

function checkRecordExists($conn, $data, $table) {
	switch($table) {
		case "academicperiod":
			$sql= "SELECT * FROM $table WHERE academicperiod= '".$data['academicperiod']. "' AND groupid= '".$data['groupid']. "'";
			break;
		case "course":
			$sql= "SELECT * FROM $table WHERE academicperiodid= '".$data['academicperiodid']. "' AND coursecode= '".$data['coursecode']. "'";
			break;
		default:
			return 0;
	}
	if($result= mysqli_query($conn, $sql)) {
		if(mysqli_num_rows($result) > 0) {
			return true;
		}
		else {
			return false;
		}
	}
	return false;
}

function deleteDirectory($dir) {
	$it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
	$files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
	foreach($files as $file) {
		if ($file->isDir()){
			rmdir($file->getRealPath());
		} else {
			unlink($file->getRealPath());
		}
	}
	if(rmdir($dir)) {
		return true;
	}
	else {
		return false;
	}
}

function getFolderDirectory($conn, $courseid, $foldername) {
	$course_record= getCourses($conn,0, $courseid);
//	print_r($course_record);
	$dir= realpath(__DIR__ . '/..') . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR. $_SESSION['groupid']. DIRECTORY_SEPARATOR.$course_record['academicperiodid'].DIRECTORY_SEPARATOR.$course_record['coursecode'].DIRECTORY_SEPARATOR.$foldername.DIRECTORY_SEPARATOR;
//	echo 'dir is=> '. $dir;
	if(!file_exists($dir)) {
		$dir= "";
//		echo 'no it doesnt';
		return $dir;
	}
	else {
//		echo 'yes it is';
		return $dir;
	}

}

function check_file_is_audio( $tmp ) {
	$allowed = array(
		'audio/mpeg', 'audio/x-mpeg', 'audio/mpeg3', 'audio/x-mpeg-3', 'audio/aiff',
		'audio/mid', 'audio/x-aiff', 'audio/x-mpequrl','audio/midi', 'audio/x-mid',
		'audio/x-midi','audio/wav','audio/x-wav','audio/xm','audio/x-aac','audio/basic',
		'audio/flac','audio/mp4','audio/x-matroska','audio/ogg','audio/s3m','audio/x-ms-wax',
		'audio/xm'
	);
	
	// check REAL MIME type
	$finfo = finfo_open(FILEINFO_MIME_TYPE);
	$type = finfo_file($finfo, $tmp );
	finfo_close($finfo);
	
	// check to see if REAL MIME type is inside $allowed array
	if( in_array($type, $allowed) ) {
		return true;
	} else {
		return false;
	}
}

function validateInput($data) {
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data);
	return $data;
}

function sendRequestApproveMail($conn, $student_email, $confirmation_link, $subject='', $title='', $message='', $smtp_settings){
	/*$smtp_host = "secure290.sgcpanel.com";
	$smtp_password = "automailer*123#";
	$smtp_username = "automailer@oandsonsnetwork.com";*/
	
	if(empty(trim($subject))){
		$subject = "Confirmation Request";
	}
	if(empty(trim($title))){
		$subject = "Confirmation Request";
	}
	if(empty(trim($message))){
		$message = "Please click on the link below to confirm your request";
	}
	
//	$sql = "SELECT * FROM settings LIMIT 1";
//	$result = mysqli_query($conn, $sql);
//	if(mysqli_num_rows($result) > 0) {
//		while ($row = mysqli_fetch_array($result)) {
//			$smtp_host = $row['smtp_host'];
//			$smtp_password = $row['smtp_password'];
//			$smtp_username = $row['smtp_username'];
//		}
//	}
	
	
	$mail = new PHPMailer(true);
	$mail->isSMTP();
	$email = $student_email;//'enenim2000@yahoo.com';
	//$mail->SMTPDebug = 2; //comment out this code for ajax request to work
	$mail->Host = $smtp_settings['smtp_host'];//'secure290.sgcpanel.com';
	$mail->Port = $smtp_settings['smtp_port'];
	$mail->SMTPAuth = true;
	$mail->Username = $smtp_settings['smtp_username'];//'automailer@oandsonsnetwork.com';
	$mail->Password =$smtp_settings['smtp_password'];//'automailer*123#';
	$mail->SMTPSecure = 'ssl';
	$mail->From = $smtp_settings['smtp_username'];//'automailer@oandsonsnetwork.com';
	$mail->FromName = "Student Digital Assignment";//ucfirst($store_name1) . ' espread application';
	$recipient_name = trim($email);
	$mail->addAddress($email); //Name is optional
	//$mail->addReplyTo("bassey@dqdemos.com","Espread System");
	$mail->isHTML(true); //Set email format to HTML
	date_default_timezone_set ("Africa/Lagos");
	$mail->Subject = "$subject";
	$mail->Body = '<div style="width: 610px;">
<div style="width: 610px;">
        <div style="width: 600px; background-color: ghostwhite; color: red; padding: 10px; text-align: center; font-weight: bold;">
            <span style="">'. $title . '</span><hr/>
        </div>
        <div style="width: 610px; background-color: white; padding: 5px; text-align: center; color: black; font-weight: bold;">
            <span>' . $message . '</span>
        </div>
        <div style="width: 610px; background-color: ghostwhite; padding: 5px; text-align: center; color: black; font-weight: bold;">
            <span class="" style="font-size: xx-large; font-weight: bold; color: orange;">&#8659;</span>
        </div>
        <div style="width: 610px; border-radius: 0; text-align: center; color: white; background-color: #3c8dbc; font-size:18px; font-weight:bold; padding: 5px;">'.  $confirmation_link . '</div>
    </div>
</div>';
	$mail->AltBody = 'Student Mobile Assistant' . $subject;
	try{
		$mail->send();
		return "success";
	}
	catch(phpmailerException $e) {
		echo 'Message: ' .$e->getMessage();
		return 'Failed';
	}

	// if($mail->send()) {
	// 	return "success";
	// }else{
	// 	return "failure";
	// }
}

function checkEmailExists($conn, $email, $groupid) {
	$sql= "SELECT email FROM student WHERE LOWER(email)= '$email' AND groupid= '$groupid'";
	if($result= mysqli_query($conn, $sql)) {
		if(mysqli_num_rows($result) > 0) {
			return true;
		}
	}
	return false;
}

function getPath($path) {
	if(strstr($path, '\\')) $path= str_replace('\\', '/', $path);
	$path_arr= explode('/uploads/', $path);
	$path= $path_arr[1];
	return $path;
}
