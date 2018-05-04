<?php
include_once('conn.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$email=$_POST["email"];
	$password=md5($_POST["password"]);
}else{

	$email=$_GET["email"];
	$password=md5($_GET["password"]);
}

$getData = "select student.*,studyprogram.groupname,studyprogram.groupshortname,studyprogram.institution,studyprogram.faculty,studyprogram.department,studyprogram.courseofstudy,studyprogram.programme from student inner join studyprogram on student.groupid=studyprogram.groupid where email='$email' and password='$password' AND student.activationstatus='ACTIVE' AND studyprogram.status='ACTIVE'";

$qur = $connection->query($getData);


$flag=0;
while($r=mysqli_fetch_assoc($qur))
{
	$jsonObj[] = array("email" => $r['email'], "name" => $r['name'], "studentid" => $r['studentid'], "groupid" => $r['groupid'], "groupname" => $r['groupname'], "groupshortname" => $r['groupshortname'],"institution" => $r['institution'], "faculty" => $r['faculty'], "department" => $r['department'], "courseofstudy" => $r['courseofstudy'], "activationstatus" => $r['activationstatus'], "Programme" => $r['programme'],"message" => "Valid", "error" => "0");

$flag=1;	
}

if ($flag==0){
	$jsonObj[] =array("message" => "Invalid", "error" => "1");
}

header('content-type: application/json');
echo json_encode($jsonObj);

@mysqli_close($conn);

?>
