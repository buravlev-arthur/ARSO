<?php
	require("mysql_connect.php");
	$data=json_decode($_POST['json_data']);
	
	if ($_GET['req']=='create'){

		$code=$data->code;
		$name=$data->name;

		$sql="SELECT count(*) FROM specials WHERE id='$code' OR name='$name'";
		$result=mysqli_query($connect,$sql);
		$row=mysqli_fetch_assoc($result);
		
		if ($row['count(*)']==0){
		$sql="INSERT INTO specials VALUES ('$code','$name')";
		mysqli_query($connect,$sql) or die (mysqli_error($connect));
		echo "OK";
		}
		else
			echo "ERROR";
	}
	
	else if ($_GET['req']=='change'){
		$special=$data->special;
		$new_name=$data->new_name;
		
		$sql="SELECT count(*) FROM specials WHERE name='$new_name'";
		$result=mysqli_query($connect,$sql);
		$row=mysqli_fetch_assoc($result);
		
		if ($row['count(*)']==0){
			$sql="UPDATE specials SET name='$new_name' WHERE id=$special";
			mysqli_query($connect,$sql) or die (mysqli_error($connect));
			echo "OK";
		}
		else
			echo "ERROR";
	}
	
	else if ($_GET['req']=='delete'){
			$special=$data->special;
			$sql="SELECT count(*) FROM groups_structure WHERE special='$special'";
			$result=mysqli_query($connect,$sql);
			$row=mysqli_fetch_assoc($result);
		
		if ($row['count(*)']==0){
			$sql="DELETE FROM specials WHERE id=$special";
			mysqli_query($connect,$sql) or die (mysqli_error($connect));
			echo "OK";
		}
		else
			echo "ERROR";
	}
?>