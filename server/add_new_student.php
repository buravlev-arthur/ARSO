<?php 
	session_start();
	include('mysql_connect.php');
	header('Content-type: text/html; charset=utf-8');
	$data = json_decode($_POST["jsonData"]);
	
	$first_name=$data->first_name;
	$last_name=$data->last_name;
	$for_father=$data->for_father;
	$burn_date=strtotime($data->burn_date);
	$male=$data->male;
	$special=$data->special;
	$start_year=$data->start_year;
	$vocation=$data->vocation;
	
	$sql_search="SELECT count(*) FROM students WHERE first_name='$first_name' AND last_name='$last_name' AND for_father='$for_father' AND burn_date='$burn_date' AND special='$special'";
	$result_search=mysqli_query($connect,$sql_search);
	$row=mysqli_fetch_assoc($result_search);
	
	if ($row['count(*)']==0){
		$sql="INSERT INTO students (first_name, last_name, for_father, burn_date, male, special, start_year, vocation) VALUES ('$first_name','$last_name','$for_father','$burn_date','$male', '$special', $start_year, $vocation)";
		mysqli_query($connect,$sql) or die (mysqli_error($connect));
		echo "OK";
	}
		else
			echo "ERROR";
	mysqli_close($connect);	
?>