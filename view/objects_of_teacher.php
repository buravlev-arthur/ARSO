<?php
	session_start();
	include('../server/mysql_connect.php');
	header('Content-type: text/html; charset=utf-8');
	mysqli_query($connect,"set names utf8");
	$teacher_id=$_SESSION['user_id'];
	$teacher=$_SESSION['user_name'];
	?>
	<script>
		$.getScript('../js/inputs.php');
		$(function(){
			//переход на страницу создания предмета
			$('#create_new_object').click(function(){
				ajax_loading('#content');
				$('#content').load('new_object.php');
			});
			//надписи для кнопок редактирования предметов
			$('.object_edit_button').mouseover(function(){
				$(this).find('.object_buttons_text').show();
			});
			$('.object_edit_button').mouseout(function(){
				$(this).find('.object_buttons_text').hide();
			});
			//открыть список занятий предмета
			$('.object_edit_lessons_open').click(function(){
				$(this).siblings('.lessons_of_object_ul').show(0,function(){
					//скроллинг списка занятий предмета
					$(this).jScrollPane();
				});
				$(this).hide();
				$(this).siblings('.object_edit_lessons_close').show();
			});
			//закрыть список занятий предмета
			$('.object_edit_lessons_close').click(function(){
					$(this).siblings('.lessons_of_object_ul').hide(0);
					$(this).hide();
					$(this).siblings('.object_edit_lessons_open').show();
			});
			//добавить новые занятия
			$('.add_lessons_buttons').click(function(){
				var id=$(this).siblings('span').text();
				ajax_loading('#content');
				$('#content').load('lessons_of_object.php?lesson='+id);
			});
			
		});
		//изменение названия предмета
		function open_rename_input(eq){
			$('.new_object_name_input').eq(eq).val($('.object_of_teacher_name').eq(eq).find('span').text());
			$('.object_of_teacher_name').eq(eq).find('span').text('');
			$('.new_object_name_block').eq(eq).show();
		}
		function rename_object_go(id,eq){
			var json={
				'id':id,
				'val':$('.new_object_name_input').eq(eq).val()
			}
			ajax_loading($('.new_object_name_block').eq(eq));
			$.ajax({
				url:'../server/rename_object.php',
				type:'POST',
				dataType:'text',
				data:'jsonData='+$.toJSON(json),
				success:function(res){
					$('.new_object_name_block').eq(eq).hide();
					$("#white_shadow").remove();
					$('.object_of_teacher_name').eq(eq).find('span').text(res);
				}
			});
		}
		//удаление предмета
		function delete_object(name,id,eq){
			var dialog_window=$("<div class='flash_dialog'><?= $teacher ?>, Вы действительно хотите удалить предмет '"+name+"'?</div>");
			var for_buttons=dialog_window.append('<div></div>');
			for_buttons.append("<button class='dialog_buttons_yes'>удалить</button>");
			for_buttons.append("<button class='dialog_buttons_no'>отмена</button>");
			dialog_window.appendTo("#containter");
			$('.dialog_buttons_no').click(function(){
				$(".flash_dialog").remove();
			});
			$('.dialog_buttons_yes').click(function(){
				ajax_loading('.flash_dialog');
				$.ajax({
					url:'../server/delete_object.php',
					type:'POST',
					dataType:'text',
					data:'id='+id,
					success:function(){
						$(".flash_dialog").remove();
						$('.object_of_teacher_block').eq(eq).remove();
					}
				});
			});
		}
	</script>
	<?php
	//поиск предметов данного преподавателя
	if ($_GET['year']=='all' or !isset($_GET['year']) and $_GET['part']=='all' or !isset($_GET['part'])){
		$sql="SELECT *, year-part_of_year as real_date FROM objects WHERE teacher=$teacher_id ORDER BY real_date";
		$result=mysqli_query($connect,$sql) or die (mysqli_error($connect));
	}
	else if ($_GET['part']=='all' or !isset($_GET['part'])){
		$sql="SELECT *, year-part_of_year as real_date FROM objects WHERE teacher=$teacher_id AND year=$_GET[year] ORDER BY real_date";
		$result=mysqli_query($connect,$sql) or die (mysqli_error($connect));
	}
	else{
		$sql="SELECT *, year-part_of_year as real_date FROM objects WHERE teacher=$teacher_id AND year=$_GET[year] AND part_of_year=$_GET[part] ORDER BY real_date";
		$result=mysqli_query($connect,$sql) or die (mysqli_error($connect));
	}
	if (mysqli_num_rows($result)==0){
		echo "<div id='report_error_allscreen'>$teacher, Для начала работы Вам необходимо создать хотя бы один предмет. Используйте кнопку ниже для создания первого предмета.</div>";
		echo "<div class='center_for_button'><button id='create_new_object' class='basic_button'>создать новый предмет</button></div>";	
	}
	else{
	?>
		<div id='objects_ul_left'>
		<div><h1 id='objects_ul_header' class='big_header' style='float:left;'>Список ваших предметов</h1>
		<button id='create_new_object' class='basic_button' style='margin-top:12px;'>создать новый предмет</button></div>
	<?php
		while ($row=mysqli_fetch_assoc($result)){
			$object_name=$row['name'];
			$object_group=$row['the_group'];
			$year=$row['year'];
			if ($row['part_of_year']==1)
				$sesson="Зимняя сессия";
			else
				$sesson="Летняя сессия";
			?> 
				<div class='object_of_teacher_block'>
					<div class='object_id' style='display:none'><?= $row['id'] ?></div>
					<div class='object_of_teacher_name'><span><?= $object_name ?></span>
					<div class='new_object_name_block'>
						<input class='new_object_name_input' type='text'></input>
						<button class='new_object_name_button' onclick="rename_object_go(<?= $row['id'] ?>,$(this).index('.new_object_name_button'))">применить</button>
					</div>
					</div>
					<div class='object_of_teacher_other_info'>
						<div class='object_of_teacher_group'><?= $object_group ?> группа</div>
						<div class='object_of_teacher_year'><?= $year ?> год, <?= $sesson ?></div>
					</div>
            
            
						<div class='object_edit_button object_edit_lessons_open'>
							<div class='object_buttons_text'>список занятий</div>
						</div>
						<div class='object_edit_button object_edit_lessons_close'>
							<div class='object_buttons_text'>закрыть список</div>
						</div>
            
						<div class='object_edit_button object_delete' onclick="delete_object('<?= $object_name ?>', '<?= $row['id'] ?>',$(this).index('.object_delete'))">
							<div class='object_buttons_text'>удалить предмет</div>
						</div>
            
						<div class='object_edit_button object_edit_object_name' onclick="open_rename_input($(this).index('.object_edit_object_name'));">
							<div class='object_buttons_text'>изменить название</div>
						</div>

            
						<div class='object_edit_button object_edit_results' onclick="ajax_loading('#content');$('#content').load('lessons_table.php?object=<?= $row['id'] ?>');">
							<div class='object_buttons_text'>оценки студентов</div>
						</div>
            
            
						<div class='lessons_of_object_ul'>
							<div class='lessons_of_object_panel'>
								<span style='display:none'><?= $row['id'] ?></span>
								<button class='add_lessons_buttons'>добавить занятия</button>
							</div>
							<?php 
								//составление внутреннего списка занятий по предмету
								$sql2="SELECT * FROM object_$row[id] ORDER BY date,time";
								$rus_mounths=Array(' ','января','февраля','марта', 'апреля','мая','июня','июля','августа','сентября','октября','ноября','декабря');
								$result2=mysqli_query($connect,$sql2);
								$j=0;
								while ($row2=mysqli_fetch_assoc($result2)){
									$j++;
									echo "<div id='$row2[id]' class='lesson_of_object'>";
										?>
										<div class='object_lesson_time' style='display:none;'><?= $row2['time'] ?></div>
										<div class='object_lesson_min' style='display:none;'><?= $row2['bals_min'] ?></div>
										<div class='object_lesson_max' style='display:none;'><?= $row2['bals_max'] ?></div>
										
										<div class='object_lesson_number'><?= $j ?></div>
										<div class='object_lesson_type'><?= $row2['type'] ?></div>
										<div class='object_lesson_data'><?= Date('d.m.Y',$row2['date']) ?> </div>
										<div class='object_lesson_name' title='<?= $row2['theme_name'] ?>'><?=$row2['theme_name'] ?></div>
										<div class='actions_for_lessons_of_object'>
                                            <div class='lesson_change_button'>изменить </div>
                                            <div class='lesson_delete_button'>удалить</div>
                                        </div>
										<?php
									echo "</div>";
								}
							?>
						</div>
				</div>
			<?php
		}
		echo "</div>";
		?> 
		<div id='sort_objects_menu_block'>
			<div id='sort_objects_menu_header'>Фильтр предметов</div>
				<ul id='objects_ul'>
				<li  id='sort_object_allobjects' onclick="ajax_loading('#objects_ul_left');$('#content').load('objects_of_teacher.php');">за все время<span>+</span></li>
			<?php 
				$sql="SELECT *, year-part_of_year as real_date FROM objects WHERE teacher=$teacher_id ORDER BY real_date";
				$result=mysqli_query($connect,$sql) or die (mysqli_error($connect));
				$object_year_ul="";
				$object_year_path_ul=-1;
				$year_count=1;
				while ($row=mysqli_fetch_assoc($result)){	
					if ($object_year_ul!=$row['year'] or $object_year_path_ul!=$row['part_of_year']){
						
						if ($object_year_ul!=$row['year']){
							if ($object_year_path_ul!=-1){
								$object_year_path_ul=-1;
								echo "</ul>";
							}
							echo "<li onclick=\"ajax_loading('#objects_ul_left');$('#content').load('objects_of_teacher.php?year=$row[year]&part=all');\">$row[year] год<span>+</span></li>";
							echo "<ul>";
							$object_year_ul=$row['year'];
							$year_count=1;
						}
						else
							$year_count++;
						
						
						if ($object_year_path_ul!=$row['part_of_year']){
							echo "<li onclick=\"ajax_loading('#objects_ul_left');$('#content').load('objects_of_teacher.php?year=$row[year]&part=$row[part_of_year]');\">";
								if ($row['part_of_year']==1)
									echo "Зимняя сессия";
								else
									echo "Летняя сессия";
							echo "</li>";
							$object_year_path_ul=$row['part_of_year'];
						}
						
					}
				}
			?>
				</ul>
				<div id='object_sort_menu_allcount'>Найдено <?= mysqli_num_rows($result) ?> предметов</div>
		</div>
		
<!-- Окно изменения информации о занятии -->
<div id='lesson_change_window'>
	<h1 class='flash_window_h'>Изменить параметры занятия</h1>
	<div>
		<label class='label_for_form_under'>Тема занятия:</label>
		<input id='new_theme_of_lesson' class='basic_text' type='text' >
	</div>
	<div>
		<label class='label_for_form_under'>Дата занятия:</label>
		<input id='new_date_of_lesson' class='input_date' type='text' >
	</div>
	<div>
		<label class='label_for_form_under'>Тип занятия:</label>
		<select id='new_type_of_lesson' class='basic_select' style='width:220px;' >
			<option value='тип занятия'>тип занятия</option>
			<option value='лекция'>лекция</option>
			<option value='семинар'>семинар</option>
			<option value='лабораторная'>лабораторная</option>
			<option value='контрольная'>контрольная</option>
			<option value='зачет'>зачет</option>
			<option value='экзамен'>экзамен</option>
		</select>
	</div>

	<div>
		<label class='label_for_form_under'>Баллы (мин/макс):</label>
		<input id='new_min_of_lesson' style='width:42px;' maxlength='3' placeholder='мин' class='basic_text' type='text' >
		<input id='new_max_of_lesson' style='width:42px;' maxlength='3' placeholder='макс' class='basic_text' type='text' >
	</div>
	<div>
		<label class='label_for_form_under'>Время проведения:</label>
		<select id='new_time_of_lesson' class='basic_select' style='width:220px;' >
			<option value='1'>1 пара</option>
			<option value='2'>2 пара</option>
			<option value='3'>3 пара</option>
			<option value='4'>4 пара</option>
			<option value='5'>5 пара</option>
			<option value='6'>6 пара</option>
		</select>
	</div>
	<div>
		<button id="lesson_save_change" class="dialog_buttons_yes" style="width:118px;">сохранить</button>
		<button id="lesson_cancel_change" class="dialog_buttons_no">отмена</button>
	</div>
</div>
<?php
	}
	mysqli_close($connect);
?>
<script>
	//изменение данных о занятии
	var lesson_id;
	var lesson;
	var object_id;
	$('.lesson_change_button').click(function(){
		lesson_id=$(this).parent().parent().attr('id');
		object_id=$(this).parent().parent().parent().parent().parent().parent().find('.object_id').text();
		lesson=$(this).parent().parent();
		$('#new_theme_of_lesson').val($(this).parent().parent().find('.object_lesson_name').text());
		$('#new_date_of_lesson').val($(this).parent().parent().find('.object_lesson_data').text());
		$('#new_min_of_lesson').val($(this).parent().parent().find('.object_lesson_min').text());
		$('#new_max_of_lesson').val($(this).parent().parent().find('.object_lesson_max').text());
		$('#new_type_of_lesson option[value="'+$(this).parent().parent().find('.object_lesson_type').text()+'"]').attr('selected','selected');
		$('#new_time_of_lesson option[value="'+$(this).parent().parent().find('.object_lesson_time').text()+'"]').attr('selected','selected');
		$('#lesson_change_window').show();
	});
	//отмена изменений
	$('#lesson_cancel_change').click(function(){
		$('#lesson_change_window').hide();
	});
	//сохранение изменений
	$('#lesson_save_change').click(function(){
		var json_data={
			'id':lesson_id,
			'object_id':object_id,
			'theme':$('#new_theme_of_lesson').val(),
			'date':$('#new_date_of_lesson').val(),
			'type':$('#new_type_of_lesson').val(),
			'bals_min':$('#new_min_of_lesson').val(),
			'bals_max':$('#new_max_of_lesson').val(),
			'time':$('#new_time_of_lesson').val()
		};
		ajax_loading("#lesson_save_change");
		$.ajax({
					url:"../server/rename_lesson.php",
					type:'POST',
					data:'jsonData='+$.toJSON(json_data),
					dataType: "text",
					success:function(res){
						$("#white_shadow").remove();
						if (res=='OK'){
							lesson.find('.object_lesson_name').text(json_data.theme);
							lesson.find('.object_lesson_data').text(json_data.date);
							lesson.find('.object_lesson_type').text(json_data.type);
							lesson.find('.object_lesson_time').text(json_data.time);
							lesson.find('.object_lesson_min').text(json_data.bals_min);
							lesson.find('.object_lesson_max').text(json_data.bals_max);
							$('#lesson_change_window').hide();
						}
						else if (res=="ERROR")
							alert("В это время у Вас уже есть занятие. Измените параметры и попробуйте снова")
						else
							alert(res);
					}
		});
	});
	
	//удаление занятия
	$('.lesson_delete_button').click(function(){
			var lesson_theme=$(this).parent().parent().find('.object_lesson_name').text();
			lesson_id=$(this).parent().parent().attr('id');
			object_id=$(this).parent().parent().parent().parent().parent().parent().find('.object_id').text();
			lesson=$(this).parent().parent();
			var dialog_window=$("<div class='flash_dialog'><?= $teacher ?>, Вы действительно хотите удалить занятие по теме '"+lesson_theme+"'?</div>");
			var for_buttons=dialog_window.append('<div></div>');
			for_buttons.append("<button class='dialog_buttons_yes'>удалить</button>");
			for_buttons.append("<button class='dialog_buttons_no'>отмена</button>");
			dialog_window.appendTo("#containter");
			
			$('.flash_dialog .dialog_buttons_no').click(function(){
				$(".flash_dialog").remove();
			});
			
			$('.flash_dialog .dialog_buttons_yes').click(function(){
				ajax_loading('.flash_dialog');
				$.ajax({
					url:'../server/delete_lesson.php',
					type:'post',
					data:'lesson_id='+lesson_id+'&object_id='+object_id,
					dataType:'text',
					success:function(result){
						$('.flash_dialog').remove();
						lesson.remove();
					}
				});
			});
	});
</script>