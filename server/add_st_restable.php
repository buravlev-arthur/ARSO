<?php 
	header("Content-type: text/html; charset=utf-8");
	require("mysql_connect.php");
	$data=json_decode($_POST['json_data']);
	
	$id=$data->st_id;
	$group=iconv("utf-8","cp1251",$data->group);
	$undergroup=$data->undergroup;
	$obj=$data->obj;
	$sql_check="SELECT students FROM results_for_lesson_$obj WHERE students=$id";
	$result=mysqli_query($connect,$sql_check) or die (mysqli_error($connect));
	if (mysqli_num_rows($result)>0)
		exit("Такой студент уже есть в данной оценочной таблице");
	else {
		$sql="INSERT INTO results_for_lesson_$obj (students, the_group, undergroup ) VALUES ($id, '$group', $undergroup )";
		mysqli_query($connect,$sql) or die (mysqli_error($connect));
		echo "OK";
	}
mysqli_close($connect);
?>