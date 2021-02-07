<?php
	session_start();
	header("Content-type: text/html; charset=utf-8;"); 
	include('../server/mysql_connect.php');
	mysqli_query($connect,"set names utf8");
	$object=$_GET['object'];
	$teacher_id=$_SESSION['user_id'];
	$teacher=$_SESSION['user_name'];
	?>
	<script>
		$('.dialog_buttons_no').die('click');
	</script>
<!-- список с группами и подгруппами -->
	<label id='select_data_results' for='for_lesson_for_group'><span>Выборка данных:</span>
		<select class='lesson_for_group' style='font-size:15px;'>
			<?php 
				$sql_object_groups="SELECT * FROM groups_of_lesson_$object";
				$result_object_groups=mysqli_query($connect,$sql_object_groups) or die (mysqli_error($connect));
				$yet=0;
				while ($row_obj_groups=mysqli_fetch_assoc($result_object_groups)){
					if (mysqli_num_rows($result_object_groups)==1 and $row_obj_groups['undergroup']==1){
							echo "<option value='0' class='0'>$row_obj_groups[the_group] группа</option>";
					}
					else{
						if ($yet==0){
							echo "<option value='0' class='0'>сводная таблица</option>";
							$yet++;
						}
						if ($row_obj_groups['undergroup']==1)
							echo "<option value='$row_obj_groups[the_group]' class='0'>$row_obj_groups[the_group] группа</option>";
						else if ($row_obj_groups['undergroup']>1){
								echo "<option value='$row_obj_groups[the_group]' class='$row_obj_groups[undergroup]0'>$row_obj_groups[the_group] группа (все)</option>";
							for ($op=1;$op<=$row_obj_groups['undergroup'];$op++)
									echo "<option value='$row_obj_groups[the_group]' class='$op'>$row_obj_groups[the_group] группа - $op подгруппа</option>";
						}
					}
				}
			?>
		</select>
	</label>
<script>
	//установка параметра выборки по умолчанию
	$(document).ready(function(){
		$(".lesson_for_group option").each(function(){
			if ($(this).val()=="<?= $_GET['group'] ?>" && $(this).attr('class')=="<?= $_GET['undergroup'] ?>"){
				$(this).attr('selected','selected');
			}
		});
	});
	// смена выборки данных
	$(".lesson_for_group").change(function(){
		var group=$(".lesson_for_group option:selected").attr('value');
		var undergroup=$(".lesson_for_group option:selected").attr('class');
			ajax_loading("#content");
			$("#content").load("lessons_table.php?object=<?= $object ?>&group="+group+"&undergroup="+undergroup);
	});
</script>
<button id="add_st_result_table">добавить студента</button>
<button id="del_st_result_table">удалить студента</button>
<div id='save_results_indicator'>Изменения результатов еще не было</div>
<?php if (!isset($_GET['group']) or $_GET['group']==0 and !isset($_GET['undergroup']) or $_GET['group']==0){ ?>
<div id='table_result_cont'>
<div id='st_name_flash'></div>
<table id='results_table' cellspacing='0'>
	<tr>
		<td style="border:0;border-bottom:1px solid #ccc;background:#fff;"> </td>
		<td style="border:0;border-bottom:1px solid #ccc;background:#fff;"> </td>
		<!-- описание дат месяцев занятий-->
		<?php
			$sql="SELECT * FROM object_$object ORDER BY date,time";
			$result=mysqli_query($connect,$sql) or die (mysqli_error($connect));
			if (mysqli_num_rows($result)==0){
				echo "<div id='report_error_allscreen'>$teacher, для данной группы или подгруппы еще не создано ни одного занятия по этому предмету.</div>";
				echo "<script>$('table').remove();</script>";
				exit();
			}
			$lessons_count=mysqli_num_rows($result);
			$month_massiv=Array("","Январь","Февраль","Март","Апрель","Май","Июнь","Июль","Август","Сентябрь","Октябрь","Ноябрь","Декарь");
			$month_of_lesson=0;
			while ($row=mysqli_fetch_assoc($result)){
				if ($month_of_lesson!=Date('n',$row['date'])){
					echo "<td style='border:0;border-bottom:1px solid #ccc;background:#fff;' ><span class='month_name_in_table'>".$month_massiv[Date('n',$row['date'])]."</span></td>";
					$month_of_lesson=Date('m',$row['date']);
				}
				else{
					echo "<td style='background:#fff;border:0;border-bottom:1px solid #ccc;'> <span class='day_of_lesson'>".Date('d',$row['date'])."</span></td>";
				}
			}
		?>
		<td style="border:0;border-bottom:1px solid #ccc;background:#fff;"> </td>
	</tr>
	<tr>
		<td rowspan='3' class='res_table_header' align='center'>Студенты</td>
		<td rowspan='3' class='res_table_header' align='center'>Группа</td>
		<!-- описание типов и дат занятий -->
		<?php 
			mysqli_data_seek($result,0);
			while ($row=mysqli_fetch_assoc($result)){
				echo "<td class='res_table_header' align='center'>";
					echo substr($row['type'],0,6);
				echo "</td>";
			}
		?>
		<td rowspan='3' class='res_table_header' align='center'>Ср.балл</td>
	</tr>
	<!-- описание групп занятий -->
	<tr>
		<?php
			mysqli_data_seek($result,0);
			while ($row=mysqli_fetch_assoc($result)){
					if ($row['the_group']==0)
						echo "<td class='result_header_name' align='center' rowspan='2'>все</td>";
					else if ($row['undergroup']==0)
						echo "<td class='result_header_name' align='center' rowspan='2'>".$row['the_group']."гр"."</td>";
					else
						echo "<td class='result_header_name' align='center'>".$row['the_group']."гр"."</td>";
			}
		?>
	</tr>
	<!-- описание подгрупп занятий -->
	<tr>
		<?php
			mysqli_data_seek($result,0);
			while ($row=mysqli_fetch_assoc($result)){
				if ($row['undergroup']!=0)
					echo "<td style='white-space:nowrap;' class='result_header_undergr'>".$row['undergroup']." подгр"."</td>";
			}
		?>
	</tr>
	
	<!-- Составление списка студентов и их результатов-->
	<?php
		$sql_stid="SELECT results_for_lesson_$object.*, students.first_name, students.last_name FROM results_for_lesson_$object, students WHERE students.id=results_for_lesson_$object.students ORDER BY the_group";
		$result_stid=mysqli_query($connect,$sql_stid) or die (mysqli_error($connect));
		$group_name_for_table="";
		while ($row_stid=mysqli_fetch_assoc($result_stid)){
			//заголовок номера группы
			if ($group_name_for_table!=$row_stid['the_group']){
				echo "<tr><td class='groups_name_restable' style='background:#E1FCCF;color:#5B6554;font-weight:bold;' colspan='".($lessons_count+3)."'>".$row_stid['the_group']." группа</td></tr>";
				$group_name_for_table=$row_stid['the_group'];
			}
			echo "<tr>";
				//имена и группы
				echo "<td class='st_res_names' style='width:190px;white-space:nowrap;' id='$row_stid[students]'>".$row_stid['first_name']." ".$row_stid['last_name']."</td>";
				echo "<td  class='st_res_gr' style='white-space:nowrap;' align='center'>".$row_stid['the_group']."гр";
				if ($row_stid['undergroup']!=0)
					echo " / ".$row_stid['undergroup']."</td>";
				else
					echo "</td>";
			
			//оценки		
			mysqli_data_seek($result,0);
			$results_summ=0;
			$results_count=0;
			while ($row=mysqli_fetch_assoc($result)){
				if ($row['the_group']==$row_stid['the_group'] and $row['undergroup']==$row_stid['undergroup']){
					echo "<td class='res_val'><span class='hideval_for_lesson'>$row[id]</span><span class='hideval_for_student'>$row_stid[students]</span><span class='bals_min'>$row[bals_min]</span><span class='bals_max'>$row[bals_max]</span><input type='text' class='result_value' maxlength='2' value='".$row_stid['id'.$row['id']]."'></td>";
					$results_summ+=$row_stid['id'.$row['id']];
					$results_count++;
				}
				else if ($row['the_group']==0){
					echo "<td class='res_val'><span class='hideval_for_lesson'>$row[id]</span><span class='hideval_for_student'>$row_stid[students]</span><span class='bals_min'>$row[bals_min]</span><span class='bals_max'>$row[bals_max]</span><input type='text' class='result_value' maxlength='2' value='".$row_stid['id'.$row['id']]."'></td>";
					$results_summ+=$row_stid['id'.$row['id']];
					$results_count++;
				}
				else if ($row['the_group']==$row_stid['the_group'] and $row['undergroup']==0){
					echo "<td class='res_val'><span class='hideval_for_lesson'>$row[id]</span><span class='hideval_for_student'>$row_stid[students]</span><span class='bals_min'>$row[bals_min]</span><span class='bals_max'>$row[bals_max]</span><input type='text' class='result_value' maxlength='2' value='".$row_stid['id'.$row['id']]."'></td>";
					$results_summ+=$row_stid['id'.$row['id']];
					$results_count++;
				}
				else
					echo "<td style='background:#E7EFF6'> </td>";
			}
			//средний балл
			echo "<td align='center'><span class='srznach__student'>$row_stid[students]</span>";
			if ($results_count!=0)
				printf("%.1f",$results_summ/$results_count);
			else
				echo "0.0";
			echo "</td>";
			echo "</tr>";
		}
	?>
</table>
</div>
<?php 
	}
	else{ ?>
	<div id='table_result_cont'>
	<div id='st_name_flash'></div>
	<table id='results_table' cellspacing='0'>
	<tr>
		<td style="border:0;border-bottom:1px solid #ccc;background:#fff;"> </td>
		<!-- описание дат месяцев занятий-->
		<?php
			if ($_GET['undergroup']>9){
				$sql="SELECT * FROM object_$object WHERE the_group='$_GET[group]' AND undergroup=0 ORDER BY date,time";
			}
			else{
				$sql="SELECT * FROM object_$object WHERE the_group='$_GET[group]' AND undergroup=$_GET[undergroup] ORDER BY date,time";
			}
			
			$result=mysqli_query($connect,$sql) or die (mysqli_error($connect));
			if (mysqli_num_rows($result)==0){
				echo "<div id='report_error_allscreen'>$teacher, для данной группы или подгруппы еще не создано ни одного занятия по этому предмету.</div>";
				echo "<script>$('table').remove();</script>";
				exit();
			}
			$lessons_count=mysqli_num_rows($result);
			$month_massiv=Array("","Январь","Февраль","Март","Апрель","Май","Июнь","Июль","Август","Сентябрь","Октябрь","Ноябрь","Декарь");
			$month_of_lesson=0;
			while ($row=mysqli_fetch_assoc($result)){
				if ($month_of_lesson!=Date('n',$row['date'])){
					echo "<td style='border:0;border-bottom:1px solid #ccc;background:#fff;' ><span class='month_name_in_table'>".$month_massiv[Date('n',$row['date'])]."</span></td>";
					$month_of_lesson=Date('m',$row['date']);
				}
				else{
					echo "<td style='background:#fff;border:0;border-bottom:1px solid #ccc;'> <span class='day_of_lesson'>".Date('d',$row['date'])."</span></td>";
				}
			}
		?>
		<td style="border:0;border-bottom:1px solid #ccc;background:#fff;"> </td>
	</tr>
	<tr>
		<td rowspan='1' class='res_table_header' align='center'>Студенты</td>
		<!-- описание типов занятий -->
		<?php
			mysqli_data_seek($result,0);
			while ($row=mysqli_fetch_assoc($result)){
				echo "<td class='res_table_header' align='center'>";
					echo substr($row['type'],0,6);
				echo "</td>";
			}
		?>
		<td rowspan='1' class='res_table_header' align='center'>Ср.балл</td>
	</tr>
	<!-- Составление списка студентов и их результатов-->
	<?php
		if ($_GET['undergroup']>9){
			$str_und="1";
			for ($s=2;$s<$_GET['undergroup'];$s++){
				$str_und.=" OR undergroup=".$s;
			}
			$sql_stid="SELECT results_for_lesson_$object.*, students.first_name, students.last_name FROM results_for_lesson_$object, students WHERE students.id=results_for_lesson_$object.students AND results_for_lesson_$object.the_group='$_GET[group]' ORDER BY the_group";
		}
		else
			$sql_stid="SELECT results_for_lesson_$object.*, students.first_name, students.last_name FROM results_for_lesson_$object, students WHERE students.id=results_for_lesson_$object.students AND results_for_lesson_$object.the_group='$_GET[group]' AND results_for_lesson_$object.undergroup=$_GET[undergroup] ORDER BY the_group";
		
		$result_stid=mysqli_query($connect,$sql_stid) or die (mysqli_error($connect));

		while ($row_stid=mysqli_fetch_assoc($result_stid)){
			echo "<tr>";
				//имена и группы
				echo "<td class='st_res_names' style='width:190px;white-space:nowrap;' id='$row_stid[students]'>".$row_stid['first_name']." ".$row_stid['last_name']."</td>";
			//оценки		
			mysqli_data_seek($result,0);
			$results_summ=0;
			$results_count=0;
			while ($row=mysqli_fetch_assoc($result)){
				if ($row['the_group']==$row_stid['the_group'] and $row['undergroup']==$row_stid['undergroup']){
					echo "<td class='res_val'><span class='hideval_for_lesson'>$row[id]</span><span class='hideval_for_student'>$row_stid[students]</span><span class='bals_min'>$row[bals_min]</span><span class='bals_max'>$row[bals_max]</span><input type='text' class='result_value' maxlength='2' value='".$row_stid['id'.$row['id']]."'></td>";
					$results_summ+=$row_stid['id'.$row['id']];
					$results_count++;
				}
				else if ($row['the_group']==0){
					echo "<td class='res_val'><span class='hideval_for_lesson'>$row[id]</span><span class='hideval_for_student'>$row_stid[students]</span><span class='bals_min'>$row[bals_min]</span><span class='bals_max'>$row[bals_max]</span><input type='text' class='result_value' maxlength='2' value='".$row_stid['id'.$row['id']]."'></td>";
					$results_summ+=$row_stid['id'.$row['id']];
					$results_count++;
				}
				else if ($row['the_group']==$row_stid['the_group'] and $row['undergroup']==0){
					echo "<td class='res_val'><span class='hideval_for_lesson'>$row[id]</span><span class='hideval_for_student'>$row_stid[students]</span><span class='bals_min'>$row[bals_min]</span><span class='bals_max'>$row[bals_max]</span><input type='text' class='result_value' maxlength='2' value='".$row_stid['id'.$row['id']]."'></td>";
					$results_summ+=$row_stid['id'.$row['id']];
					$results_count++;
				}
				else
					echo "<td style='background:#E7EFF6'> </td>";
			}
			//средний балл
			echo "<td align='center'><span class='srznach__student'>$row_stid[students]</span>";
			printf("%.1f",$results_summ/$results_count);
			echo "</td>";
			echo "</tr>";
		}
	?>
</table>
</div>
<?php	}
?>

<div id='del_stud_restable_block'>
	<select id='del_stud_restable' class='basic_select'>
		<script>
			$(".st_res_names").each(function(){
				$("<option id='"+$(this).attr('id')+"'>"+$(this).text()+"</option>").appendTo("#del_stud_restable");
			});
		</script>
	</select>
	<button class='dialog_buttons_yes'>удалить</button>
	<button class='dialog_buttons_no'>отмена</button>
</div>
<div id='add_stud_restable_block'>
	<label for="search_students" style='right:12px' id="search_st_label">Укажите студента:<input autofocus placeholder="Фамилия и имя" id="search_students" type="type"><button id="start_search_stud_button">искать</button></label>
	<div id='search_st_result_restable' class='0'>Поиска студента еще не было.</div>
	<select id='gr_sel_for_new_st' class='basic_select'> 
	<?php 
			$sql_object_groups="SELECT * FROM groups_of_lesson_$object";
			$result_object_groups=mysqli_query($connect,$sql_object_groups) or die (mysqli_error($connect));
			
			while ($row_obj_groups=mysqli_fetch_assoc($result_object_groups)){
				if ($row_obj_groups['undergroup']==1)
					echo "<option value='$row_obj_groups[the_group]' class='0'>$row_obj_groups[the_group] группа</option>";
				else if ($row_obj_groups['undergroup']>1){
					for ($op=1;$op<=$row_obj_groups['undergroup'];$op++)
							echo "<option value='$row_obj_groups[the_group]' class='$op'>$row_obj_groups[the_group] группа - $op подгруппа</option>";
				}
			}

	?>
	</select>
	<button style='position:absolute;width:84px;right:92px;bottom:-10px;' class='dialog_buttons_yes'>добавить</button>
	<button style='position:absolute;right:5px;bottom:-10px;' class='dialog_buttons_no'>отмена</button>
</div>
<script>
	//для удаления студентов
	$('#del_st_result_table').click(function(){
		$('#del_stud_restable_block').show();
	});
	$('.dialog_buttons_no').live('click',function(){
			$(this).parent().hide();
	});
	$('#del_stud_restable_block .dialog_buttons_yes').click(function(){
		var st_id=$("#del_stud_restable option:selected").attr('id');
		$.ajax({
				url:"../server/del_student_from_resulttable.php?id="+st_id+"&object=<?= $object ?>",
				type:'GET',
				dataType: "text",
				success:function(res){
					ajax_loading("#del_stud_restable_block");
					$("#content").load("<?= $_SERVER['REQUEST_URI'] ?>");
				}
		});
	});
	//для добавления студентов
	$("#add_st_result_table").click(function(){
		$("#add_stud_restable_block").show();
	});
	$('#search_students').keydown(function(event){
						if (event.which==13)
							go_to_search_students();
		});
		$('#start_search_stud_button').click(function(){
			go_to_search_students();
		});
		
		function go_to_search_students(){
			ajax_loading("#search_st_label");
			if ($("#search_students").val()!="" && $("#search_students").val()!=" "){
				$.ajax({
					url:"../server/search_students.php?search="+$("#search_students").val(),
					type:'GET',
					dataType: "text",
					success:function(res){
						$("#white_shadow").remove();
						if (res=="ERROR")
							alert("Вы ввели некорретный запрос.")
						else{
							$('#search_st_result_restable').remove();
							$(res).insertAfter("#search_st_label");
							$("#search_students").blur();
							
						}
					}
				});
			}
		}
		//отправка данных о добавляемом студенте
		$('#add_stud_restable_block .dialog_buttons_yes').click(function(){
			if ($('#search_st_result_restable').attr('class')!="0"){
				var json_data={
					'st_id':$('#search_st_result_restable').attr('class'),
					'group':$("#gr_sel_for_new_st option:selected").val(),
					'undergroup':$("#gr_sel_for_new_st option:selected").attr('class'),
					'obj':<?= $object ?>
				};
				$.ajax({
					url:"../server/add_st_restable.php",
					type:'POST',
					data:'json_data='+$.toJSON(json_data),
					dataType: "text",
					success:function(res){
						if (res=="OK"){
							ajax_loading("#content");
							$("#content").load("<?= $_SERVER['REQUEST_URI'] ?>");
						}
						else{
							alert(res);
						}
					}
				});
			}
			else
				alert("Вы не осуществили поиск студента");
			
		});
	
	//cкролинг таблицы
	$("#table_result_cont").jScrollPane();
	//курсор
	
	$("#table_result_cont .res_val").mousemove(function(e){
		var offset=$("#results_table").offset();
		$('#st_name_flash').css({ 
			'left':(e.pageX-offset.left+22)+"px",
			'top':(e.pageY-offset.top-10)+"px",
			'display':'block'
		});
		$('#st_name_flash').text($(this).parent().find(".st_res_names").text()+"  "+$(this).parent().find(".st_res_gr").text());
	});
	$("#table_result_cont .res_val").mouseout(function(){
		$('#st_name_flash').hide();
	});
	
	//активация текстового поля с оценкой
	var prevent_value=0;
	$(".result_value").click(function(){
		$(this).select();
		prevent_value=$(this).val();
	});
	//ввод клашиви ентер
	$('.result_value').keydown(function(event){
		if (event.which==13){
			$(this).blur();
		}
	});
	//подсвечивание tr
	$("#results_table tr").mouseover(function(){
		$(this).css('background','#e5e5e5');
	});
	$("#results_table tr").mouseout(function(){
		$(this).css('background','');
	});
	//подготовка к сохранению результата
	$('.result_value').focusout(function(){
			var id_less=$(this).parent().find('.hideval_for_lesson').text();
			var id_stud=$(this).parent().find('.hideval_for_student').text();
			var min=$(this).parent().find('.bals_min').text();
			var max=$(this).parent().find('.bals_max').text();
			var value=$(this).val();
			if (parseInt(value)<parseInt(min) || parseInt(value)>parseInt(max)){
				alert("Минимальный балл: "+min+"\n"+"Максимальный балл: "+max);
				$(this).val(prevent_value);
			}
			else
				save_result(id_less,id_stud,value,min,max);
	});
	//cохранение результата
	function save_result(id_less,id_stud,val,min,max){
		$.ajax({
				url:"../server/change_result_value.php?object="+<?= $object ?>+"&lesson="+id_less+"&student="+id_stud+"&value="+val,
				type:'GET',
				dataType: "text",
				success:function(res){
					$('#save_results_indicator').text(res);
					$('#save_results_indicator').animate({
						'width':'50px'
					},300,function(){
						$('#save_results_indicator').animate({
							'width':'210px'
						},300);
					});
					//пересчет среднего значения
					$('.srznach__student').each(function(){
						if (parseInt($(this).text())==parseInt(id_stud)){
							var sr_znach_box=$(this).parent();
							var new_summ=0;
							var count=0;
							sr_znach_box.parent().find('.res_val').each(function(){
								 new_summ+=parseInt( $(this).find('input').val());
								 count++;
							});
							new_summ=(new_summ/count).toFixed(1);
							sr_znach_box.html("<span class='srznach__student'>"+id_stud+"</span>"+new_summ);
						}
					});
				}
		});
	}
</script>
<?php
	mysqli_close($connect);
?>