<?php 
header('Content-Type: text/html; charset=utf-8');
require('mysql_connect.php');
mysqli_query($connect,"set names utf8");

$group_id=$_GET['group'];
$object_year=$_GET['year'];
$object_year_part=$_GET['part'];

//определяем курс и специальность для заданной группы
$sql="SELECT course, special FROM groups_structure WHERE id='$group_id'";
$result=mysqli_query($connect,$sql) or die (mysqli_error($connect));
$cs=mysqli_fetch_assoc($result);

//находим всех студентов с таким курсов и специальностью
if ($object_year_part==2){
	$start_year=$object_year-$cs['course'];
	$sql="SELECT id,first_name,last_name FROM students WHERE start_year+vocation=$start_year AND special=$cs[special] ORDER BY first_name";

}
else{
	$start_year=$object_year-$cs['course']+1;
	$sql="SELECT id,first_name,last_name FROM students WHERE start_year+vocation=$start_year AND special=$cs[special] ORDER BY first_name";

}
$result=mysqli_query($connect,$sql) or die (mysqli_error($connect));


$students=array();
$i=0;
$temp_str="";
while ($st=mysqli_fetch_assoc($result)){
	$temp_str.="<div id='$st[id]' class='undergroup_student_ul'>".$st['first_name']." ".$st['last_name']."</div>";
}
if ($temp_str!="")
	echo $temp_str;
else
	echo "ERROR";
mysqli_close($connect);
?>