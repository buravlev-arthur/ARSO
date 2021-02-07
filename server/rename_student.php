<?php 
	session_start();
	include('mysql_connect.php');
	$data = json_decode($_POST["jsonData"]);
	$id=$_GET[id];
	$first_name=$data->first_name;
	$last_name=$data->last_name;
	$for_father=$data->for_father;
	$burn_date=strtotime($data->burn_date);
	$male=$data->male;
	$special=$data->special;
	$start_year=$data->start_year;
	$vocation=$data->vocation;
	
	$sql_search="SELECT count(*) FROM students WHERE first_name='$first_name' AND last_name='$last_name' AND for_father='$for_father' AND burn_date='$burn_date' AND special='$special' AND id<>$id";
	$result_search=mysqli_query($connect,$sql_search);
	$row=mysqli_fetch_assoc($result_search);
	
	if ($row['count(*)']==0){
		$sql="UPDATE students SET first_name='$first_name', last_name='$last_name', for_father='$for_father', burn_date='$burn_date', male='$male', special='$special', start_year=$start_year, vocation=$vocation WHERE id=$id";
		mysqli_query($connect,$sql) or die (mysqli_error());
		echo "OK";
	}
		else
			echo "ERROR";
	mysqli_close($connect);	
?>