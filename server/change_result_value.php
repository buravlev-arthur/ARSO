<?php 
header('Content-Type: text/html; charset=utf-8');
require('mysql_connect.php');
$object=$_GET['object'];
$value=$_GET['value'];
$lesson=$_GET['lesson'];
$student=$_GET['student'];
$sql="UPDATE results_for_lesson_$object SET id$lesson=$value WHERE students=$student";
mysqli_query($connect,$sql) or die (mysqli_error($connect));
echo "изменения сохранены в ".Date('H:i:s');
mysqli_close($connect);
?>