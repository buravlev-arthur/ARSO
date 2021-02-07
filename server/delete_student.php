<?php
	include "mysql_connect.php";
	$sql="DELETE FROM students WHERE id='$_POST[id]'";
	mysqli_query($connect,$sql) or die(mysqli_error($connect));
?>