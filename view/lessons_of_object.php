<?php
	header('Content-type: text/html; charset=utf-8');
	include ('../server/mysql_connect.php');
	mysqli_query($connect,"set names utf8");
	$lesson_table=$_GET['lesson'];
	//echo strtotime("04.03.91 10:45");
	$sql="SELECT count(*) FROM object_$lesson_table";
	$result=mysqli_query($connect,$sql);
	$row=mysqli_fetch_assoc($result);
?>
<script>
	$.getScript('../js/inputs.php');
	var lessons=<?= $row['count(*)'] ?>;
	
	//добавление новой формы
	function add_lesson_form(){
		lessons=lessons+1;
		var new_lesson_block=$("#new_lesson_blocks_0").clone().attr('id','new_lesson_blocks_'+lessons);
		$('#plus_lesson_block').before(new_lesson_block);
		$("#new_lesson_blocks_"+lessons+" .lesson_numbers").text('занятие '+lessons);
		$("#new_lesson_blocks_"+lessons+" .input_date").remove();
		$("#new_lesson_blocks_"+lessons+" .ui-datepicker-trigger").remove();
		$("#new_lesson_blocks_"+lessons).append("<input type='text' style='width:120px' class='input_date' placeholder='дата занятия'></div>");
		$("#new_lesson_blocks_"+lessons+" .input_date").datepicker({dateFormat:"dd.mm.yy",showOn:"both",buttonImage:"../js/jquery-ui/css/smoothness/images/calendar.gif",buttonImageOnly:true,dayNamesMin: ["Вс","Пн","Вт","Ср","Чт","Пт","Сб"],
		monthNames:["Январь","Февраль","Март","Апрель","Май","Июнь","Июль","Август","Сентябрь","Октябрь","Ноябрь","Декабрь"],changeMonth: true, changeYear: true});	
		if (lessons>1)
		$("#new_lesson_blocks_"+(lessons-1)+" .lesson_delete").remove();
		event_detection();
		};
		//переинициализация событий для новой формы
	function event_detection(){
		$('#new_lesson_blocks_'+lessons+' .lesson_delete').mouseover(function(){
			$('.lesson_delete_text').eq($(this).index('.lesson_delete')).show();
		});
		$('#new_lesson_blocks_'+lessons+' .lesson_delete').mouseout(function(){
			$('.lesson_delete_text').eq($(this).index('.lesson_delete')).hide();
		});
		$('#new_lesson_blocks_'+lessons+' .lesson_delete').click(function(){
			$("#new_lesson_blocks_"+lessons).remove();
			if (lessons>1)
				$("#new_lesson_blocks_0 .lesson_delete").clone().appendTo("#new_lesson_blocks_"+(lessons-1));
			lessons=lessons-1;
			event_detection();
			});
	}
	//document_ready
	$(function(){
		$('#plus_lesson_block').click(function(){
			add_lesson_form();
		});
		add_lesson_form();
		
		
		$('#go_insert_lessons').click(function(){
			var json=[];
			for (var i=1;i<=$(".new_lesson_blocks").length-1;i++){
				json[i]={
						'theme':$("#new_lesson_blocks_"+(<?= $row['count(*)'] ?>+i)+" .basic_text").eq(0).val(),
						'type':$("#new_lesson_blocks_"+(<?= $row['count(*)'] ?>+i)+" .type_lesson").val(),
						'min':$("#new_lesson_blocks_"+(<?= $row['count(*)'] ?>+i)+" .basic_text").eq(1).val(),
						'max':$("#new_lesson_blocks_"+(<?= $row['count(*)'] ?>+i)+" .basic_text").eq(2).val(),
						'date':$("#new_lesson_blocks_"+(<?= $row['count(*)'] ?>+i)+" .input_date").val(),
						'the_group':$("#new_lesson_blocks_"+(<?= $row['count(*)'] ?>+i)+" .lesson_for_group").val(),
						'undergroup':$("#new_lesson_blocks_"+(<?= $row['count(*)'] ?>+i)+" .lesson_for_group option:selected").attr('class'),
						'lesson_number':$("#new_lesson_blocks_"+(<?= $row['count(*)'] ?>+i)+" .lesson_time").val()
					}
			}
				ajax_loading("#content");
				$.ajax({
					url:"../server/add_lessons.php?lesson=<?= $lesson_table ?>",
					type:'POST',
					data:'jsonData='+$.toJSON(json),
					dataType: "text",
					success:function(res){
						if (res=='1')
							$('#content').load('lessons_table.php?object=<?= $lesson_table ?>&q=0');
						else{
							$("#white_shadow").remove();
							alert(res);
						}
					}
				});
			});

		});
</script>
<?php
	if ($row['count(*)']!=0){
		echo "<div id='lessons_start_quantity'>На данный момент предмет уже имеет <b>".$row['count(*)']."</b> занятий. <span>вернуться к списку предметов</span></div>";
		?>
			<script>
				$('#lessons_start_quantity>span').click(function(){
					$('#content').load('objects_of_teacher.php');
				});
			</script>
		<?php
	}
	else
		echo "<div id='lessons_start_quantity'>На данный момент ещё не создано ни одного занятия.</div>";
?>
<div class='new_lesson_blocks' id='new_lesson_blocks_0'>
	<div class='lesson_numbers'>занятие 1</div>
	<label class='for_lesson_for_group' for='for_lesson_for_group'><span>для кого:</span>
	<select class='lesson_for_group'>
		<option value='0' class='0'>все вместе</option>
		<?php 
			$sql_object_groups="SELECT * FROM groups_of_lesson_$lesson_table";
			$result_object_groups=mysqli_query($connect,$sql_object_groups) or die (mysqli_error($connect));
			
			while ($row_obj_groups=mysqli_fetch_assoc($result_object_groups)){
				if ($row_obj_groups['undergroup']==1)
					echo "<option value='$row_obj_groups[the_group]' class='0'>$row_obj_groups[the_group] группа</option>";
				else if ($row_obj_groups['undergroup']>1){
						echo "<option value='$row_obj_groups[the_group]' class='0'>$row_obj_groups[the_group] группа (все)</option>";
					for ($op=1;$op<=$row_obj_groups['undergroup'];$op++)
							echo "<option value='$row_obj_groups[the_group]' class='$op'>$row_obj_groups[the_group] группа - $op подгруппа</option>";
				}
			}

		?>
	</select></label>
	<select class='basic_selects type_lesson'>
		<option>тип занятия</option>
		<option>лекция</option>
		<option>семинар</option>
		<option>лабораторная</option>
		<option>контрольная</option>
		<option>зачет</option>
		<option>экзамен</option>
	</select>
	<input type='text' class='basic_text' maxlength='70' placeholder='тема занятия'></input>
	<label>баллы</label><input type='text' maxlength='3' style='width:35px' class='basic_text' placeholder='мин'></input>
	<input type='text' style='width:40px; margin-right:10px;' maxlength='3' class='basic_text'  placeholder='макс'></input>
	<input type='text' style='width:120px' class='input_date' placeholder='дата занятия'></input>
	<select class='basic_selects lesson_time'>
		<option>пара №</option>
		<option>1 пара</option>
		<option>2 пара</option>
		<option>3 пара</option>
		<option>4 пара</option>
		<option>5 пара</option>
		<option>6 пара</option>
	</select>
	<div class='lesson_delete'>
		<div class='lesson_delete_text'>удалить занятие</div>
	</div>
</div>
<div id='plus_lesson_block'><span>добавить занятие</span></div>
<button id='go_insert_lessons' class='basic_button'>создать занятия</button>
<?php 
	mysqli_close($connect);
?>