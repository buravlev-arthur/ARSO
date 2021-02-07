<?php 
	session_start();
	include('mysql_connect.php');
	header('Content-type: text/html; charset=utf-8');
	$data = json_decode($_POST["jsonData"]);
	$theme=$data->theme;
	$id=$data->id;
	$id_obj=$data->object_id;
	$type=$data->type;
	$date=strtotime($data->date);
	$bals_min=$data->bals_min;
	$bals_max=$data->bals_max;
	$time=$data->time;
	
	$sql_search="SELECT count(*) FROM object_$id_obj WHERE time=$time AND date='$date' AND id<>$id";
	$result_search=mysqli_query($connect,$sql_search);
	$row=mysqli_fetch_assoc($result_search);
	
	if ($row['count(*)']==0){
		$sql="UPDATE object_$id_obj SET theme_name='$theme', date='$date', type='$type', time='$time', bals_min=$bals_min, bals_max=$bals_max WHERE id=$id";
		mysqli_query($connect,$sql) or die (mysqli_error($connect));
		echo "OK";
	}
		else
			echo "ERROR";
	mysqli_close($connect);	
?>