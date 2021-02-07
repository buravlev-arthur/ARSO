<?php
session_start();
header('Content-type: text/html; charset=utf-8');
include('../server/mysql_connect.php');
mysqli_query($connect,"set names utf8");
$teacher_id=$_SESSION['user_id'];
$teacher=$_SESSION['user_name'];
//GET
$year=$_GET["year"];
$semestr=$_GET["sem"];
$obj=$_GET["obj"];
$gr=$_GET["gr"];
?>
<script>
		$.getScript('../js/inputs.php');
</script>

<?php
//определить семестр
$gr_dates=Array(); //массив с месяцами и средними баллами в них

//предметы преподавателя
if ($obj==0)
	$all_objects="SELECT * from objects WHERE teacher=$teacher_id AND year='$year' AND part_of_year=$semestr";
else
	$all_objects="SELECT * from objects WHERE teacher=$teacher_id AND year='$year' AND part_of_year=$semestr AND id=$obj";
$all_objects_res=mysqli_query($connect,$all_objects);
?>
<div id='top_statistic_block'>
	<div class='tb_object_count'>всего предметов
		<div class='tb_object_count_val'><?= mysqli_num_rows($all_objects_res) ?></div>
	</div>
<?php
$all_lessons_count=0; //число созданных занятий
$lessons_finished=0; //число отведенных занятий (прошедшие даты) 
$students=Array(); //массив с данными о студентах
$stud_num=0;
$obj_and_gr=Array(); //массив для выбора предмета и группы
$obj_and_gr_index=0;
$groups=Array();

//cоставляем массив для выбора предмета и группы
$sel_allobject=mysqli_query($connect,"SELECT * from objects WHERE teacher=$teacher_id AND year='$year' AND part_of_year=$semestr;");
while($row_sel_objects=mysqli_fetch_assoc($sel_allobject)){
	$obj_and_gr[$obj_and_gr_index][0]=$row_sel_objects['id'];
	$obj_and_gr[$obj_and_gr_index][1]=$row_sel_objects['name'];
	$sel_allgroups=mysqli_query("SELECT * FROM groups_of_lesson_$row_sel_objects[id]");
	$gi=2;
	while ($row_sel_groups=mysqli_fetch_assoc($sel_allgroups)){
		$obj_and_gr[$obj_and_gr_index][$gi]=$row_sel_groups['the_group'];
		$gi++;
	}
	$obj_and_gr_index++;
}

while ($row_allobjects=mysqli_fetch_assoc($all_objects_res)){
	//составляем массив обучаемых групп
		$groups_of_object=explode(",",$row_allobjects['the_group']);
		for ($i=0;$i<count($groups_of_object);$i++){
			if (!in_array($groups_of_object[$i],$groups))
				$groups[]=$groups_of_object[$i];
		}
	//все занятия по предметам
	if ($gr==0)
		$all_lessons="SELECT * FROM object_$row_allobjects[id]"; //таблица с занятиями
	else
		$all_lessons="SELECT * FROM object_$row_allobjects[id] WHERE the_group='$gr' OR the_group='0'";
		
	$all_lessons_res=mysqli_query($connect,$all_lessons);
	if ($gr==0)
		$stud_of_object="SELECT * FROM results_for_lesson_$row_allobjects[id]"; //таблица с оценками
	else
		$stud_of_object="SELECT * FROM results_for_lesson_$row_allobjects[id] WHERE the_group='$gr'";
	$stud_of_object_res=mysqli_query($connect,$stud_of_object) or die (mysqli_error($connect));
	$id_lessons=Array();
	while ($row_alllessons=mysqli_fetch_assoc($all_lessons_res)){
		$all_lessons_count++;
		//поиск уже пройденные занятия
		$id_lessons[]=$row_alllessons['id'];
		if ($row_alllessons['date']<strtotime("now"))
			$lessons_finished++;
		//средний балл за месяц
		$temp_balls_sr=0;
		$temp_balls_count=0;
		while ($row_balls_of_object=mysqli_fetch_assoc($stud_of_object_res)){
			$temp_balls_sr+=$row_balls_of_object['id'.$row_alllessons['id']];
			$temp_balls_count++;
		}
		mysqli_data_seek($stud_of_object_res,0);
		$temp_balls_sr=$temp_balls_sr;
		$gr_dates[Date('n',$row_alllessons['date'])][0]+=$temp_balls_sr;
		$gr_dates[Date('n',$row_alllessons['date'])][1]+=$temp_balls_count;
	}
	//обучаемые студенты и их суммарный балл
	while ($row_stud_of_object=mysqli_fetch_assoc($stud_of_object_res)){
		$stud_balls_summ=0; //сумма баллов студента
			$already_st=-1;
			//проверяем нет ли уже такого студента
			for ($k=0;$k<count($students);$k++){
				if ($students[$k][0]==$row_stud_of_object['students'])
					$already_st=$k;
			}
			if ($already_st==-1){
				$students[$stud_num][0]=$row_stud_of_object['students'];
				$students[$stud_num][1]=$row_stud_of_object['the_group'];
				for ($i=0;$i<count($id_lessons);$i++){
					$stud_balls_summ+=$row_stud_of_object['id'.$id_lessons[$i]];
				}
				$students[$stud_num][2]=$stud_balls_summ;
				$students[$stud_num][3]++;
				$stud_num++;
			}
			else{
				for ($i=0;$i<count($id_lessons);$i++){
					$stud_balls_summ+=$row_stud_of_object['id'.$id_lessons[$i]];
				}
				$students[$already_st][2]=$students[$already_st][2]+$stud_balls_summ;
				$students[$already_st][3]++;
			}
	}
	$obj_and_gr_index++;
	unset($id_lessons);
}

//определение среднего балла каждого студента
for ($i=0;$i<count($students);$i++)
	$students[$i][2]=$students[$i][2]/$students[$i][3];
	
//поиск первого и последнего месяца и ср.балла за каждый из них
$min_month=0;
$max_month=0;
for ($m=1;$m<=12;$m++){
	if ($gr_dates[$m][1]!=0){
		$gr_dates[$m][0]=$gr_dates[$m][0]/$gr_dates[$m][1];
		if ($min_month==0)
			$min_month=$m;
		if ($m>$max_month)
			$max_month=$m;
	}
}
//сортировка суммы баллов по убыванию
for ($i=0;$i<count($students);$i++){
	for ($j=0;$j<count($students);$j++){
		if ($students[$i][2]>=$students[$j][2]){
			$temp_mass=$students[$i];
			$students[$i]=$students[$j];
			$students[$j]=$temp_mass;
		}
	}
}
//для круговой диаграммы
$balls_diapason=$students[0][2]-$students[count($students)-1][2];
$balls_quart=(int)($balls_diapason/4);
$balls_now=$students[count($students)-1][2]+$balls_quart;
$radial_diagram_mass=Array();
for ($i=0;$i<4;$i++){
	$for_this_ball=0;
	if ($i==0){
		$radial_diagram_mass[$i][0]=(int)$students[count($students)-1][2]."-".$balls_now." баллов";
		for ($j=0;$j<count($students);$j++)
		if ($students[$j][2]>=$students[count($students)-1][2] and $students[$j][2]<$balls_now)
			$for_this_ball++;
	}
	else if ($i==3){
		$radial_diagram_mass[$i][0]=$balls_now."-".(int)$students[0][2]." баллов";
		for ($j=0;$j<count($students);$j++)
		if ($students[$j][2]>$balls_now and $students[$j][2]<=$students[0][2])
			$for_this_ball++;
	}
	else{
		$radial_diagram_mass[$i][0]=$balls_now."-".($balls_now+$balls_quart)." баллов";
		for ($j=0;$j<count($students);$j++)
		if ($students[$j][2]>=$balls_now and $students[$j][2]<($balls_now+$balls_quart))
			$for_this_ball++;
		$balls_now+=$balls_quart;
	}
	$radial_diagram_mass[$i][1]=$for_this_ball;
}

//для гистограммы максимальных баллов
$st_names="SELECT id,first_name,last_name FROM students WHERE";
if (count($students)<8)
	$all_count_for_cunter=count($students)-1;
else
	$all_count_for_cunter=7;
	
for ($i=0;$i<$all_count_for_cunter;$i++){
	$st_names.=" id=".$students[$i][0]." OR";
}
	$st_names.=" id=".$students[$all_count_for_cunter][0];
$st_names_res=mysqli_query($connect,$st_names) or die (mysqli_error($connect));

while ($row_st_names=mysqli_fetch_assoc($st_names_res)){
	for ($i=0;$i<count($students);$i++){
		if ($students[$i][0]==$row_st_names['id']){
			$students[$i][3]=$row_st_names['first_name']." ".$row_st_names['last_name'];
			$students[$i][2]=(int)$students[$i][2];
		}
	}
}
?> 
	<div class='tb_object_count' style='width:142px'>количество занятий
		<div class='tb_object_count_val'><?= $all_lessons_count ?></div>
	</div>
	<div id='tb_other_main_info_block'>
		<div class='other_main_info_item'>отведено часов <div><?= $lessons_finished ?></div></div>
		<div class='other_main_info_item'>обучаемых групп <div><?php
		if ($gr==0)
			echo count($groups);
		else
			echo 1;
		?></div></div>
		<div class='other_main_info_item'>учащихся студентов <div><?= count($students) ?></div></div>
	</div>
<?php
	
?>
	<div id='tb_status_block'>
		<select class='stat_select' style='width:200px;' id='stat_object_sel'>
			<option value='0'>Все предметы</option>
			<?php 
				for ($ch=0;$ch<count($obj_and_gr);$ch++){
					echo "<option value=".$obj_and_gr[$ch][0].">".$obj_and_gr[$ch][1]."</option>";
				}
			?>
		</select>
		<select id='sel_0' class='stat_select active_sel' style='width:160px;'>
			<option value='0'>Все группы</option>
			<?php 
				for ($ch=0;$ch<count($groups);$ch++){
					echo "<option value='".$groups[$ch]."'>".$groups[$ch]."</option>"; 
				}
			?>
		</select>
			<?php 
				for ($ch=0;$ch<count($obj_and_gr);$ch++){
					echo "<select class='stat_select deactive_sel' id='sel_".($ch+1)."' class='stat_select' style='width:160px;'>";
						echo "<option value='0'>Все группы</option>";
						for ($ch_gr=2;$ch_gr<count($obj_and_gr[$ch]);$ch_gr++){
							echo "<option value='".$obj_and_gr[$ch][$ch_gr]."'>".$obj_and_gr[$ch][$ch_gr]."</option>";
						}
					echo "</select>";
				}
			?>
		<button id='stat_select_button' style='width:160px;'>получить статистику</button>
		<div id='about_stat'>
		<?php 
			if ($obj==0 and $gr==0)
				echo "Общая статистика";
			else if ($obj==0 and $gr!=0)
				echo "$gr группа";
			else if ($obj!=0 and $gr==0){
				for ($ks=0;$ks<count($obj_and_gr);$ks++)
					if ($obj_and_gr[$ks][0]==$obj)
						echo $obj_and_gr[$ks][1];
				
			}
			else{
				for ($ks=0;$ks<count($obj_and_gr);$ks++)
					if ($obj_and_gr[$ks][0]==$obj)
						echo $obj_and_gr[$ks][1].", $gr гр.";
			}
		?>
		</div>
	</div>
	<script>
		//выбор группы и предмета
		var active_sel=0;
		$('#stat_object_sel').change(function(){
			var sel=$('#stat_object_sel option:selected').index('#stat_object_sel option');
			$('#sel_'+active_sel).removeClass('active_sel').addClass('deactive_sel');
			$('#sel_'+sel).removeClass('deactive_sel').addClass('active_sel');
			active_sel=sel;
		});
		$("#stat_select_button").click(function(){
			ajax_loading("#tb_status_block");
			var obj=$('#stat_object_sel option:selected').val();
			var gr=$('#sel_'+active_sel).val();
			$('#content').load('statistic.php?year=<?= $year ?>&sem=<?= $semestr ?>&obj='+obj+'&gr='+gr);
		});
	</script>
	<!--<div id='tb_selects_block'>
		<span id='stat_week'>неделя</span>
		<span id='stat_month'>месяц</span>
		<span id='stat_year'>семестр</span>
	</div>-->
</div>
<div id="main_graphic" style="height:250px;width:905px;margin-left:12px;"></div>
<script>
	<?php 
		echo "var sr_balls=[";
		//echo "['2014-05-24 4:00PM', 2],['2014-05-26 4:00PM',25],['2014-05-27 4:00PM',13.1],['2014-05-28 4:00PM',33.6],['2014-05-29 4:00PM',85.9],['2014-05-30 4:00PM', 134],['2014-06-01 4:00PM',119.9],['2014-06-05 4:00PM',13.1],['2014-06-07 4:00PM',33.6],['2014-06-08 4:00PM',85.9],['2014-06-09 4:00PM',119.9]"; 
		for ($gm=$min_month;$gm<=$max_month;$gm++){
			if ($gm!=$min_month) echo ",";
			if ($gm<10)
				$this_month='0'.$gm;
			else
				$this_month=$gm;
			echo "['".Date('Y')."-".$this_month."-15 4:00PM',".$gr_dates[$gm][0]."]";
		};
		echo "];";
	?>
	$.jqplot('main_graphic',[sr_balls],
	{ 	title:'Общая успеваемость студентов в этом семестре',
	    axesDefaults: {
			labelRenderer: $.jqplot.CanvasAxisLabelRenderer
		},
		/*legend: {
            show: true,
            placement: 'outsideGrid'
        },*/
		seriesDefaults:{
			pointLabels: {show: true}
		},
		axes:{yaxis:{label:"средний балл",min:0},
			  xaxis:{renderer:$.jqplot.DateAxisRenderer,
					 tickOptions:{formatString:'%b'},}},
		series:[{color:'#FF8000'}]
	});
</script>
<div id='balls_raiting' style='height:340px;width:480px;margin:40px 10px;float:left;'></div>
<script>
	var balls_raiting = [['<?= $students[0][3] ?>', <?= $students[0][2] ?>], ['<?= $students[1][3] ?>', <?= $students[1][2] ?>], ['<?= $students[2][3] ?>', <?= $students[2][2] ?>], 
	['<?= $students[3][3] ?>', <?= $students[3][2] ?>], ['<?= $students[4][3] ?>', <?= $students[4][2] ?>], 
	['<?= $students[5][3] ?>', <?= $students[5][2] ?>], ['<?= $students[6][3] ?>', <?= $students[6][2] ?>], ['<?= $students[7][3] ?>', <?= $students[7][2] ?>]];
 
	var plot1 = $.jqplot('balls_raiting', [balls_raiting], {
    title: 'Студенты с наибольшей суммой баллов',
    series:[{renderer:$.jqplot.BarRenderer}],
    axesDefaults: {
        tickRenderer: $.jqplot.CanvasAxisTickRenderer,
		labelRenderer: $.jqplot.CanvasAxisLabelRenderer,
        tickOptions: {
          angle: -30,
          fontSize: '10pt'
        }
    },
	seriesDefaults:{
      rendererOptions: {
          barMargin: 15, 
      },
      pointLabels: {show: true}
    },
    axes: {
      xaxis: {
        renderer: $.jqplot.CategoryAxisRenderer
      },
	  yaxis:{
		label:"баллы",min:0
	  }
    }
  });
</script>

<div id='lessons_statistic' style='height:340px;width:410px;margin:40px 10px;float:left;'></div>
<script>
var data = [['<?= $radial_diagram_mass[0][0] ?>', <?= $radial_diagram_mass[0][1] ?>],['<?= $radial_diagram_mass[1][0] ?>', <?= $radial_diagram_mass[1][1] ?>], ['<?= $radial_diagram_mass[2][0] ?>', <?= $radial_diagram_mass[2][1] ?>],['<?= $radial_diagram_mass[3][0] ?>', <?= $radial_diagram_mass[3][1] ?>]];
	jQuery.jqplot('lessons_statistic', [data], 
    { title:"Успеваемость учащихся",
      seriesDefaults: {
        // Make this a pie chart.
        renderer: jQuery.jqplot.PieRenderer, 
        rendererOptions: {
          // Put data labels on the pie slices.
          // By default, labels show the percentage of the slice.
          showDataLabels: true
        }
      }, 
      legend: { show:true, location: 'e' }
    }
  );
</script>
<?php 
	mysqli_close($connect);
?>