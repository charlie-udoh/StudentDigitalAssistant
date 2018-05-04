<?php
include_once('conn.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$groupid=$_POST["groupid"];
	$academicperiodid=$_POST["academicperiodid"];
	$courseid=$_POST["courseid"];
}else{
	$groupid=$_GET["groupid"];
	$academicperiodid=$_GET["academicperiodid"];
	$courseid=$_GET["courseid"];
}

$getData = "select filemetadata.* from filemetadata inner join course on filemetadata.courseid=course.courseid inner join academicperiod on course.academicperiodid = academicperiod.academicperiodid where academicperiod.groupid='$groupid' AND course.academicperiodid='$academicperiodid' AND academicperiod.groupid='$groupid' AND filemetadata.courseid='$courseid'";

$qur = $connection->query($getData);

while($r=mysqli_fetch_assoc($qur))
{
	$flag='1';
  	$jsonObj[] = array("fileid" => $r['fileid'], "filename" => $r['filename'], "folder" => $r['folder'], "who" => $r['who'], "message" => "Valid", "error" => "0");
$flag=1;	
}

if ($flag==0){
	$jsonObj[] =array("message" => "Invalid", "error" => "1");
}

header('content-type: application/json');
echo json_encode($jsonObj);

@mysqli_close($conn);

?>



