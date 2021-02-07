<?php 
	header("Content-type: text/html; charset=utf-8");
	require("mysql_connect.php");
	$data=json_decode($_POST['json_data']);
	
	$name=$data->name;
	$special=$data->special;
	$course=$data->course;

	//проверим, нет ли уже группы с таким курсом и специальность
	$already_exists=0;
	$sql_exists="SELECT count(*) FROM groups_structure WHERE special='$special' AND course=$course OR id='$name'";
	$result_exists=mysqli_query($connect,$sql_exists) or die (mysqli_error($connect));
	
	$row=mysqli_fetch_assoc($result_exists);
	if ($row['count(*)']==0){
		$sql="INSERT INTO groups_structure VALUES ('$name','$special',$course)";
		mysqli_query($connect,$sql) or die (mysqli_error($connect));
		echo "OK";
	}
	else
		echo "ERROR";
	
	mysqli_close($connect);
?>