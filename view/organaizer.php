<?php
session_start();
header('Content-type: text/html; charset=utf-8');
include('../server/mysql_connect.php');
mysqli_query($connect,"set names utf8");
$teacher_id=$_SESSION['user_id'];
$teacher=$_SESSION['user_name'];
$time=$_GET['time'];
$days_mass=Array('','понедельник','вторник','среда','четверг','пятница','суббота','воскресенье');
$months_mass=Array('','января','февраля','марта','апреля','мая','июня','июля','августа','сентября','октября','ноября','декабря');
$months_mass2=Array('','январь','февраль','март','апрель','май','июнь','июль','август','сентябрь','октябрь','ноябрь','декабрь');

?>
<script>
		$.getScript('../js/inputs.php');
</script>
<div id='org_top_block'>
	<div id='org_time_nav_block'>
		<span onclick="ajax_loading('#content');$('#content').load('organaizer.php?time=<?= $time-(60*60*24*7) ?>');">< предыдущая неделя</span> |
		<span onclick="ajax_loading('#content');$('#content').load('organaizer.php?time=<?= $time+(60*60*24*7) ?>');">следующая неделя ></span>
	</div>
	<div id='org_date_head'><?php echo $months_mass2[Date("n",$time)].", ".Date("Y",$time)." год" ?></div>
	<div id='week_input_block'>
		<label>календарь:</label>
		<input type='text' id='week_data_input' class='input_date' />
	</div>
</div>
<script>
	$("#week_data_input").change(function(){
			var date=$("#week_data_input").val()
			var year=parseInt(date.substr(6,4));
			var day=parseInt(date.substr(0,2));
			var month=parseInt(date.substr(3,2))-1;
			var unixtime=new Date(year,month,day,1,0,0,0).getTime();
			ajax_loading('#content');
			unixtime=unixtime/1000;
			$('#content').load('organaizer.php?time='+unixtime);
	});
</script>
<div id='org_monitor'>
	<table id='org_panel' cellspacing='0'>
	<?php
		
		//определение интервала недели
		$day=Date('N',$time);
		$week_start=$time-(($day-1)*(60*60*24));
		$week_finish=$time+((7-$day)*(60*60*24));
		
		//составление массива занятий
		$less_year=Date('Y',$time);
		if (Date('m',$time)>8)
			$less_sem=1;
		else
			$less_sem=2;
			
		//время занятий
		$less_time=Array('','8:00-9:35','9:45-11:20','11:30-13:05','13:30-15:05','15:15-16:50','17:00-18:35');
		$lessons=Array(); //массив занятий для данной недели
		$li=0; //идекс массива занятий
		$all_objects="SELECT * FROM objects WHERE teacher=$teacher_id AND year='$less_year' AND part_of_year=$less_sem";
		$all_objects_res=mysqli_query($connect,$all_objects) or die(mysqli_error($connect));
		while ($row_all_objects=mysqli_fetch_assoc($all_objects_res)){
			$lessons_of_object_res=mysqli_query($connect,"SELECT * FROM object_$row_all_objects[id]");
			//поиск занятий на этой неделе
			while ($row_lessons_of_obj=mysqli_fetch_assoc($lessons_of_object_res)){
				if (
						Date("d.m.Y",$row_lessons_of_obj['date'])==Date("d.m.Y",$week_start) OR
						Date("d.m.Y",$row_lessons_of_obj['date'])==Date("d.m.Y",$week_start+(60*60*24)) OR
						Date("d.m.Y",$row_lessons_of_obj['date'])==Date("d.m.Y",$week_start+((60*60*24)*2)) OR
						Date("d.m.Y",$row_lessons_of_obj['date'])==Date("d.m.Y",$week_start+((60*60*24)*3)) OR
						Date("d.m.Y",$row_lessons_of_obj['date'])==Date("d.m.Y",$week_start+((60*60*24)*4)) OR
						Date("d.m.Y",$row_lessons_of_obj['date'])==Date("d.m.Y",$week_start+((60*60*24)*5)) OR
						Date("d.m.Y",$row_lessons_of_obj['date'])==Date("d.m.Y",$week_finish)
					)
				{
					$lessons[Date("N",$row_lessons_of_obj['date'])][$row_lessons_of_obj['time']]['object']=$row_all_objects['name'];
					$lessons[Date("N",$row_lessons_of_obj['date'])][$row_lessons_of_obj['time']]['date']=$row_lessons_of_obj['date'];
					$lessons[Date("N",$row_lessons_of_obj['date'])][$row_lessons_of_obj['time']]['time']=$row_lessons_of_obj['time'];
					$lessons[Date("N",$row_lessons_of_obj['date'])][$row_lessons_of_obj['time']]['type']=$row_lessons_of_obj['type'];
					$lessons[Date("N",$row_lessons_of_obj['date'])][$row_lessons_of_obj['time']]['theme']=$row_lessons_of_obj['theme_name'];
					if ($row_lessons_of_obj['the_group']!=0)
						$lessons[Date("N",$row_lessons_of_obj['date'])][$row_lessons_of_obj['time']]['group']=$row_lessons_of_obj['the_group']." гр.";
					else
						$lessons[Date("N",$row_lessons_of_obj['date'])][$row_lessons_of_obj['time']]['group']=$row_all_objects['the_group']." гр.";
					$lessons[Date("N",$row_lessons_of_obj['date'])][$row_lessons_of_obj['time']]['under']=$row_lessons_of_obj['undergroup']." подгруппа";
				}
			}
			
		}

		//шапка с датами
		echo "<tr>";
		echo '<th id="week_num">'.Date("W",$time).'<br>неделя</th>';
		for ($i=$week_start;$i<=$week_finish;$i+=(60*60*24)){
			if (Date('d.m.Y',$i)==Date('d.m.Y'))
				echo "<th class='org_tab_dates' style='background:#ef7700; color:#fff;'><span style='color:#fff;'>".Date('j',$i)." ".$months_mass[Date('n',$i)]."</span><br>".$days_mass[Date('N',$i)]."</th>";
			else
				echo "<th class='org_tab_dates'><span>".Date('j',$i)." ".$months_mass[Date('n',$i)]."</span><br>".$days_mass[Date('N',$i)]."</th>";
		}
		echo "</tr>";
		//ячейки для занятий
		for ($i=1;$i<=6;$i++){
			echo "<tr id='para_$i'>";
				echo "<td><span class='pari'>$i пара</span><br>$less_time[$i]</td>";
				for ($j=1;$j<=7;$j++){
					if ($j==Date('N') and Date('W',$time)==Date('W')){
						echo "<td class='day_$j' style='background:#FFF5D2; border-bottom:1px solid #f3d37a'>";
							if (isset($lessons[$j][$i])){
								echo "<div class='tab_less_block_active'>";
									echo "<div class='tab_less_object'>".$lessons[$j][$i]['object']."</div>";
									echo "<div class='tab_less_type'>".$lessons[$j][$i]['type']."</div>";
									echo "<div class='tab_less_theme'>".$lessons[$j][$i]['theme']."</div>";
									if ($lessons[$j][$i]['group']!=0)
										echo "<div class='tab_less_group'>".$lessons[$j][$i]['group']."</div>";
										
									if ($lessons[$j][$i]['under']!=0)
										echo "<div class='tab_less_under'>".$lessons[$j][$i]['under']."</div>";	
								echo "</div>";
							}
							else
								echo "<div class='tab_less_block_hide'></div>";
						echo "</td>";
					}
					else{
						echo "<td class='day_$j'>";
							if (isset($lessons[$j][$i])){
								echo "<div class='tab_less_block'>";
									echo "<div class='tab_less_object'>".$lessons[$j][$i]['object']."</div>";
									echo "<div class='tab_less_type'>".$lessons[$j][$i]['type']."</div>";
									echo "<div class='tab_less_theme'>".$lessons[$j][$i]['theme']."</div>";
									if ($lessons[$j][$i]['group']!=0)
										echo "<div class='tab_less_group'>".$lessons[$j][$i]['group']."</div>";
									if ($lessons[$j][$i]['under']!=0)
										echo "<div class='tab_less_under'>".$lessons[$j][$i]['under']."</div>";	
								echo "</div>";								
							}
							else
								echo "<div class='tab_less_block_hide'></div>";
						echo "</td>";
					}
				}
			echo "</tr>";
		}
	?>
	</table>
</div>