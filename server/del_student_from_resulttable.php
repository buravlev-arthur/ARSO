<?php
	include "mysql_connect.php";
	$object=$_GET['object'];
	$id=$_GET['id'];
	$sql="DELETE FROM results_for_lesson_$object WHERE students=$id";
	mysqli_query($connect,$sql) or die(mysqli_error($connect));
?>