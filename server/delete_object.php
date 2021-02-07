<?php 
include('mysql_connect.php');
header('Content-type: text/html; charset=utf-8');
$id=$_POST['id'];
$sql="DELETE FROM objects WHERE id='$id'";
mysqli_query($connect,$sql) or die (mysqli_error($connect));
$sql="DROP TABLE object_".$id;
mysqli_query($connect,$sql) or die (mysqli_error($connect));
$sql="DROP TABLE results_for_lesson_".$id;
mysqli_query($connect,$sql) or die (mysqli_error($connect));
$sql="DROP TABLE groups_of_lesson_".$id;
mysqli_query($connect,$sql) or die (mysqli_error($connect));
mysqli_close($connect);
?>