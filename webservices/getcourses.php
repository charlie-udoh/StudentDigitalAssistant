<?php
include_once('conn.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$groupid=$_POST["groupid"];
	$academicperiodid=$_POST["academicperiodid"];
}else{
	$groupid=$_GET["groupid"];
	$academicperiodid=$_GET["academicperiodid"];
}

$getData = "select course.* from course inner join academicperiod on course.academicperiodid = academicperiod.academicperiodid where  course.academicperiodid='$academicperiodid' AND academicperiod.groupid='$groupid'";

$qur = $connection->query($getData);
//echo $getData;

$flag=0;

while($r=mysqli_fetch_assoc($qur))
{
  	$jsonObj[] = array("courseid" => $r['courseid'], "coursecode" => $r['coursecode'], "coursename" => $r['coursename'], "courseshortname" => $r['courseshortname'], "lecturer" => $r['lecturer'], "message" => "Valid", "error" => "0");
$flag=1;	
}

if ($flag==0){
	$jsonObj[] =array("message" => "Invalid", "error" => "1");
}


header('content-type: application/json');
echo json_encode($jsonObj);

@mysqli_close($conn);

?>


