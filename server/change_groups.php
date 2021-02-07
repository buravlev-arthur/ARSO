<?php
	require('mysql_connect.php');
	$date=json_decode($_POST['json_data']);
	
	$after_name=$date->after_name;
	$name=$date->name;
	$special=$date->special;
	$course=$date->course;
	//проверим, нет ли уже группы с таким курсом и специальностью
	$already_exists=0;
	$sql_exists="SELECT count(*) FROM groups_structure WHERE special='$special' AND course=$course AND id<>'$after_name' OR id='$name' AND id<>'$after_name'";
	$result_exists=mysqli_query($connect,$sql_exists);
	$row=mysqli_fetch_assoc($result_exists);
	if ($row['count(*)']==0){
		$sql="UPDATE groups_structure SET id='$name', special='$special', course=$course WHERE id='$after_name'";
		mysqli_query($connect,$sql) or die (mysqli_error($connect));
		
		$sql3="SELECT id FROM objects WHERE the_group LIKE '%$after_name%'";
		$result3=mysqli_query($connect,$sql3) or die (mysqli_error($connect));
		while ($row3=mysqli_fetch_assoc($connect,$result3)){
		
			$sql_groups_of_lesson="UPDATE groups_of_lesson_$row3[id] SET the_group='$name' WHERE the_group='$after_name'";
			mysqli_query($connect,$sql_groups_of_lesson) or die (mysqli_error($connect));
			
			$sql_groups_of_lesson="UPDATE object_$row3[id] SET the_group='$name' WHERE the_group='$after_name'";
			mysqli_query($connect,$sql_groups_of_lesson) or die (mysqli_error($connect));
			
			$sql_groups_of_lesson="UPDATE results_for_lesson_$row3[id] SET the_group='$name' WHERE the_group='$after_name'";
			mysqli_query($connect,$sql_groups_of_lesson) or die (mysqli_error($connect));
		}
		//доработать
		$sql1="SELECT * FROM objects";
		$result1=mysqli_query($connect,$sql1) or die (mysqli_error($connect));
		while ($row1=mysqli_fetch_assoc($result1)){
			
			if ($row1['the_group']==$after_name){
				$sql2="UPDATE objects SET the_group='$name' WHERE id=$row1[id]";
				mysqli_query($connect,$sql2) or die (mysqli_error($connect));
			}
			else if (substr_count($row1['the_group'],$after_name)!=0){
				$new_groups_ul=str_replace($after_name,$name,$row1['the_group']);
				$sql2="UPDATE objects SET the_group='$new_groups_ul' WHERE id=$row1[id]";
				mysqli_query($connect,$sql2) or die (mysqli_error($connect));
			}
		
		}
		echo "OK";
	}
	else
		echo "ERROR";
		
	mysqli_close($connect);
?>