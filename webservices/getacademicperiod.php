<?php
include_once('conn.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$groupid=$_POST["groupid"];
}else{
	$groupid=$_GET["groupid"];
}

$getData = "select academicperiod.* from academicperiod where academicperiod.groupid='$groupid' ORDER BY datecreated desc";

$qur = $connection->query($getData);

$flag='0';

while($r=mysqli_fetch_assoc($qur))
{

 	$jsonObj[] = array("academicperiodid" => $r['academicperiodid'], "academicperiod" => $r['academicperiod'], "message" => "Valid", "error" => "0");
$flag=1;	
}

if ($flag==0){
	$jsonObj[] =array("message" => "Invalid", "error" => "1");
}

header('content-type: application/json');
echo json_encode($jsonObj);

@mysqli_close($conn);

?>

