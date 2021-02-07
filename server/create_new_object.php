<?php
header('Content-Type: text/html; charset=utf-8');
require('mysql_connect.php');
$data = json_decode($_POST['jsonData']);
$name=$data->name;
if ($data->part_of_year=='Зимняя сессия')
	$part_of_year=1;
else
	$part_of_year=2;
$year=$data->year;
$group_number=$data->groups;
$undergroups=$data->undergroups;
$teacher=$data->teacher;

//создаем строку из названий групп
$gr_tb_name="";
for ($i=0;$i<count($group_number);$i++){
	if ($i>0)
		$gr_tb_name.=",";
	$gr_tb_name.=$group_number[$i][0];
}
$gr_tb_name=$gr_tb_name;

//добавляем новый предмет в общую таблицу предметов
$sql="INSERT INTO objects (name,teacher,the_group,year,part_of_year) VALUES ('$name', '$teacher', '$gr_tb_name', '$year', '$part_of_year')";
mysqli_query($connect,$sql) or die ('<div id="main_error"><div id="header_of_main_error">предмет не создана</div>произошла ошибка, предмет не был создан. обратитесь к разработчику программы</div>');
//узнаем id нового предмета
$sql="SELECT id FROM objects ORDER BY id DESC";
$result=mysqli_query($connect,$sql);
$row=mysqli_fetch_assoc($result);
$lesson_id=$row['id'];
//создаем таблицу для занятий предмета
$sql = "
	CREATE TABLE object_".$lesson_id." (
		id int NOT NULL auto_increment,
		type varchar(30) NOT NULL,
		theme_name varchar(120) NOT NULL,
		bals_min int NOT NULL,
		bals_max int NOT NULL,
		date varchar(20),
		time int,
		the_group varchar(120),
		undergroup int,
		PRIMARY KEY (id)
	)";
mysqli_query($connect,$sql) or die (mysqli_error($connect));

//создаем таблицу для оценок по урокам предмета
$sql = "
	CREATE TABLE results_for_lesson_".$lesson_id." (
		students int,
		the_group varchar(120),
		undergroup int,
		PRIMARY KEY (students)
	)";
mysqli_query($connect,$sql) or die (mysqli_error($connect));

//cоздаем таблицу с иерархией групп
$sql = "
	CREATE TABLE groups_of_lesson_".$lesson_id." (
		the_group varchar(120),
		undergroup int,
		PRIMARY KEY (the_group)
	)";
mysqli_query($connect,$sql) or die (mysqli_error($connect));

//заполняем таблицу с иерархией групп
for ($i=0;$i<count($group_number);$i++){
	$group_name=$group_number[$i][0];
	$under_count=$group_number[$i][1];
	$sql="INSERT INTO groups_of_lesson_".$lesson_id." (the_group, undergroup) VALUES('$group_name',$under_count)";
	mysqli_query($connect,$sql) or die(mysqli_error($connect));
}


//заполнение таблицы оценок id студентов данной группы
$flag=true;


for ($i=0;$i<count($group_number);$i++){
	//определяем курс и специальность для заданной группы
	$group_id=$group_number[$i][0];
	$sql="SELECT course, special FROM groups_structure WHERE id='$group_id'";
	$result=mysqli_query($connect,$sql) or die (mysqli_error($connect)." поиск курса и специальности");
	$cs=mysqli_fetch_assoc($result);
	
	//находим студентов этой группы
	if ($part_of_year==2){
		$start_year=$year-$cs['course'];
		$sql_students="SELECT id FROM students WHERE start_year+vocation=$start_year AND special=$cs[special] ORDER BY first_name";

	}
	else{
		$start_year=$year-$cs['course']+1;
		$sql_students="SELECT id FROM students WHERE start_year+vocation=$start_year AND special=$cs[special] ORDER BY first_name";

	}
	$result_students=mysqli_query($connect,$sql_students) or die (mysqli_error($connect)." поиск студентов для группы");
	
	//добавляем студентов в таблицу с оценками
	$count_of_students=0;
	while ($row_student=mysqli_fetch_assoc($result_students)){
		$count_of_students++;
		if ($group_number[$i][1]==1){
			$ug=0;
			$sql_student_insert="INSERT INTO results_for_lesson_".$lesson_id." (students, the_group, undergroup) VALUES ($row_student[id], '$group_id', $ug)";
			mysqli_query($connect,$sql_student_insert) or die (mysqli_error($connect)." заполнение таблицы с оценками студентами");
		}
		else{
			$ug=$undergroups[$i][$row_student['id']];
			$sql_student_insert="INSERT INTO results_for_lesson_".$lesson_id." (students, the_group, undergroup) VALUES ($row_student[id], '$group_id', $ug)";
			mysqli_query($connect,$sql_student_insert) or die (mysqli_error($connect)." заполнение таблицы с оценками студентами");
		}
		$count_of_students++;
	}
	if ($count_of_students==0)
		$flag=false;
}








if ($flag)
	echo $lesson_id;
else{
	//если нет студентов для данного занятия - удаляем все созданные таблицы и записи и выводим сообщение
	$sql="DELETE FROM objects WHERE id='$lesson_id'";
	mysqli_query($connect,$sql) or die (mysqli_error($connect));
	$sql="DROP TABLE object_".$lesson_id;
	mysqli_query($connect,$sql) or die (mysqli_error($connect));
	$sql="DROP TABLE results_for_lesson_".$lesson_id;
	mysqli_query($connect,$sql) or die (mysqli_error($connect));
	$sql="DROP TABLE groups_of_lesson_".$lesson_id;
	mysqli_query($connect,$sql) or die (mysqli_error($connect));
	echo "no_students";
}
mysqli_close($connect);
?>