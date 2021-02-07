<?php
session_start();
include "mysql_connect.php";
$data = json_decode($_POST["jsonData"]);
$lesson=$_GET['lesson'];
for ($i=1;$i<count($data);$i++){
	$type=$data[$i]->type;
	$theme=$data[$i]->theme;
	$min=$data[$i]->min;
	$max=$data[$i]->max;
	$the_group=$data[$i]->the_group;
	$undergroup=$data[$i]->undergroup;

	switch ($data[$i]->lesson_number){
		case '1 пара': $time=1; break;
		case '2 пара': $time=2; break;
		case '3 пара': $time=3; break;
		case '4 пара': $time=4; break;
		case '5 пара': $time=5; break;
		case '6 пара': $time=6; break;
		default: exit("Вы не выбрали номер занятия.");
	}
	$date=strtotime($data[$i]->date);
	//добавляем новое занятие в таблицу занатий по предмету
	$sql="INSERT INTO object_$lesson (type, theme_name, bals_min, bals_max, date, time, the_group, undergroup) VALUES ('$type', '$theme', $min, $max, '$date', $time, '$the_group', $undergroup)";
	mysqli_query($connect,$sql) or die (mysqli_error($connect));
	
	//узнаем id нового занятие в таблице занятий
	$sql="SELECT id FROM object_$lesson ORDER BY id DESC";
	$res=mysqli_query($connect,$sql);
	$row=mysqli_fetch_assoc($res);
	
	//добавляем колонку для оценок в оценочную таблицу
	$sql="ALTER TABLE results_for_lesson_$lesson ADD COLUMN id$row[id] INT NOT NULL";
	mysqli_query($connect,$sql) or die(mysqli_error($connect));
}
	echo '1';
	mysqli_close($connect);
?>