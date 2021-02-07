<?php
	include "mysql_connect.php";
	$sql="DELETE FROM object_$_POST[object_id] WHERE id='$_POST[lesson_id]'";
	mysqli_query($connect,$sql) or die(mysqli_error($connect));
	
	$sql="ALTER TABLE results_for_lesson_$_POST[object_id] DROP id$_POST[lesson_id]";
	mysqli_query($connect,$sql) or die(mysqli_error($connect));
?>