<?php 
	session_start();
	include('../server/mysql_connect.php');
	$teacher=$_SESSION['user_name'];
	$teacher_id=$_SESSION['user_id'];
	
?>
<div id='groups_ul_left'>
		<h1 id='objects_ul_header' class='big_header'>Список существующих групп</h1>
<?php
	$sql_groups="SELECT * FROM groups_structure ORDER BY id";
		$result_groups=mysqli_query($connect,$sql_groups);
	$sql_students="SELECT first_name, special, start_year, vocation FROM students";
		$result_students=mysqli_query($connect,$sql_students);
	?>
		<table id='groups_ul_item'>
			<tr>
				<th>Статус</th>
				<th>Группа</th>
				<th>Специальность</th>
				<th>Курс</th>
				<th>Редактирование</th>
			</tr>
	<?php
	while($row=mysqli_fetch_assoc($result_groups)){
		?>
			<tr>
				<?php
					$student_exists=false;
					while($row_stud=mysqli_fetch_assoc($result_students)){
						//определить на каком курсе сейчас студент
						if (Date('m')<9){
							$course=Date('Y')-($row_stud['start_year']+$row_stud['vocation']);
						}
						else{
							$course=(Date('Y')-($row_stud['start_year']+$row_stud['vocation']))+1;
						}
						//определить, если в группе студенты в этом году
						if ($row_stud['special']==$row['special'] and $row['course']==$course){
							$student_exists=true;
							break;	
						}
					}
					if (mysqli_num_rows($result_students)>0)
						mysqli_data_seek($result_students,0);
					//вставляем нужный индикатор
					if ($student_exists){
							echo "<td class='g_table_status_blue'><div></div> </td>";
							$group_remove=0;
						}
						else{
							echo "<td class='g_table_status_red'><div></div> </td>";
							$group_remove=1;
						}
				?>
				<td class='g_table_group'><?= $row['id'] ?></td>
				<td class='g_table_special'><?php
				$sql2="SELECT name FROM specials WHERE id=$row[special]";
				$result2=mysqli_query($connect,$sql2);
				$row2=mysqli_fetch_assoc($result2);
				echo $row2['name'];
				?></td>
				<td class='g_table_course'><?= $row['course'] ?></td>
				<td class='g_table_change'><span onclick="change_group('<?= $row['id'] ?>',<?= $row['special'] ?>,<?= $row['course'] ?>,<?= $group_remove ?>,$(this).parent().index('.g_table_change'));">изменить</span> <?php 
				if (!$student_exists)
						echo "<div onclick=\"remove_group('$row[id]',$(this).parent().index('.g_table_change'))\">X</div>";
				?></td>
			</tr>
		<?php
			$student_exists=false;
	}
		?>
		</table>
		<div id='group_change_indicators'>
			<div id='g_ch_indicator_red'>в группе нет студентов</div>
			<div id='g_ch_indicator_blue'>в группе есть студенты</div>
			<div id='g_ch_indicator_x'><div>X</div>удалить группу</div>
		</div>
</div>

<!--правый блок-->
<div class='right_block_middle' style="margin:0;border:0;box-shadow:none;">
	<div id='new_group_block' class='right_block_middle' style="width:245px;">
		<h3 class='right_block_middle_header'>Новая группа</h3>
		<div class='right_block_middle_inputs'>
			<label class='label_for_form_under'>Название группы:</label>
			<input id='new_group_name' type='text' class='basic_text' placeholder="название группы">
			<label class='label_for_form_under'>Специальность:</label>
			<select id='new_group_special' class='basic_select' style='width:220px;' >
				<?php
					$sql_allspecials="SELECT id,name FROM specials";
					$result_allspecials=mysqli_query($connect,$sql_allspecials);
					while ($row_allspecials=mysqli_fetch_assoc($result_allspecials)){
						echo "<option value='$row_allspecials[id]'>$row_allspecials[id] - $row_allspecials[name]</option>";
					}
					mysqli_data_seek($result_allspecials,0);
				?>
			</select>
			<label class='label_for_form_under'>Курс:</label>
			<select id='new_group_course' class='basic_select'>
				<option value='1'>1</option>
				<option value='2'>2</option>
				<option value='3'>3</option>
				<option value='4'>4</option>
				<option value='5'>5</option>
				<option value='6'>6</option>
			</select>
		</div>
		<button id="create_new_group_button" class='basic_button'>создать группу</button>
	</div>
	
	
	
	<div id='new_special_block' class='right_block_middle' style="width:245px;">
		<h3 class='right_block_middle_header'>Специальности</h3>

			<div id='choose_special_radios' class="basic_radios">
				<input type="radio" id="radio1" name='create_special' checked="checked" /><label class='for_basic_text' for="radio1">Создать</label>
				<input type="radio" id="radio2" name='create_special'  /><label class='for_basic_text' for="radio2">Изменить</label>
			</div>
			
			
			<div id='special_create_block' class='right_block_middle_inputs'>
				<label class='label_for_form_under'>Название специальности:</label>
				<input id='new_speclial_name' type='text' class='basic_text' placeholder="название">
				<label class='label_for_form_under'>Код специальности:</label>
				<input id='new_special_code' type='text' class='basic_text' placeholder="код" maxlength="5" style="width:80px">
				<button id='create_new_special' class='basic_button'>создать специальность</button>
			</div>
			
			<div id='special_change_block' class='right_block_middle_inputs' style="display:none">
				<label class='label_for_form_under'>Специальность:</label>
				<select id='change_special_select' class='basic_select' style='width:220px;' >
					<?php
						while ($row_allspecials=mysqli_fetch_assoc($result_allspecials)){
							echo "<option value='$row_allspecials[id]'>$row_allspecials[id] - $row_allspecials[name]</option>";
						}
						mysqli_data_seek($result_allspecials,0);
					?>
				</select>
				<div id='delete_special'>удалить специальность</div>
				<label class='label_for_form_under'>Новое название:</label>
				<input id='new_name_of_special' type='text' class='basic_text' placeholder="название">
				<button id='change_new_special' class='basic_button'>изменить название</button>
			</div>
		
		
		</div>
</div>
<!--правый блок конец-->

<!-- Окно изменение информации о группе -->
<div id='group_change_window'>
	<h1 class='flash_window_h'>Изменить параметры группы</h1>
	<div>
		<label class='label_for_form_under'>Название группы:</label>
		<input id='new_name_of_group' class='basic_text' type='text' >
	</div>
	<div>
		<label class='label_for_form_under'>Специальность:</label>
		<select id='new_special_of_group' class='basic_select' style='width:220px;' >
			<?php
				while ($row_allspecials=mysqli_fetch_assoc($result_allspecials)){
					echo "<option value='$row_allspecials[id]'>$row_allspecials[id] - $row_allspecials[name]</option>";
				}
			?>
		</select>
	</div>
	<div>
		<label class='label_for_form_under'>Курс:</label>
		<select id='new_course_of_group' class='basic_select'>
			<option value='1'>1</option>
			<option value='2'>2</option>
			<option value='3'>3</option>
			<option value='4'>4</option>
			<option value='5'>5</option>
			<option value='6'>6</option>
		</select>
	</div>
	<div>
		<button id="group_save_change" class="dialog_buttons_yes" style="width:118px;">сохранить</button>
		<button id="group_cancel_change" class="dialog_buttons_no">отмена</button>
	</div>
</div>

	<script>
		$.getScript('../js/inputs.php');
		//при наведении tr таблицы подсвечивается
		$('#groups_ul_item tr').hover(function(){
			$(this).css('background','#eee');
		},function(){
			$(this).css('background','#fff');
		});
		
		//изменить информацию о группе
		var change_group_eq=0;
		var сhange_group_name="";
		var add_change_group=0;
		function change_group(group,special,course,add_change,eq){
			change_group_eq=eq;
			сhange_group_name=group;
			add_change_group=add_change;
			$('#new_name_of_group').val(group);
			$("#new_course_of_group option[value='"+course+"']").attr('selected','selected');
			$("#new_special_of_group option[value='"+special+"']").attr('selected','selected');
			//блокировка запрещенных изменений
			if (add_change==0){
				$("#new_course_of_group option").attr('disabled','disabled');
				$("#new_special_of_group option").attr('disabled','disabled');
			}
			else{
				$("#new_course_of_group option").removeAttr('disabled');
				$("#new_special_of_group option").removeAttr('disabled');
			}	
			$('#group_change_window').fadeIn(30);
				
		
		};	//cохранение изменений
			$("#group_save_change").click(function(){
				var json_data={
					after_name:сhange_group_name,
					name:$('#new_name_of_group').val(),
					special:$("#new_special_of_group").val(),
					course:$("#new_course_of_group").val()
				}
				if (json_data.name.length>1){
					ajax_loading('#group_save_change');
					$.ajax({
						url:"../server/change_groups.php",
						dataType:"text",
						type:"POST",
						data:"json_data="+$.toJSON(json_data),
						success:function(result){
							$('#white_shadow').remove();
							if (result=="OK"){
								$('.g_table_group').eq(change_group_eq).text(json_data.name);
								$('.g_table_special').eq(change_group_eq).text($("#new_special_of_group [value='"+json_data.special+"']").text());
								$('.g_table_course').eq(change_group_eq).text(json_data.course);
								
								//изменить onclick параметры
								$(".g_table_change").eq(change_group_eq).find("span").attr("onclick","change_group('"+json_data.name+"',"+json_data.special+","+json_data.course+","+add_change_group+","+change_group_eq+")");
								if (add_change_group==1)
									$('.g_table_change>div').attr("onclick","remove_group('"+json_data.name+"',"+change_group_eq+")");
								$('#group_change_window').hide();
								ajax_loading('#content');
								$('#content').load('groups.php');
							}
							else if (result=="ERROR"){
								alert("Группа с такой специальностью и курсом или названием уже существует. Измените данные параметры.");
								$('#group_change_window').hide();
							}
							else{
							alert(result);
							}
						}
					});
				}
				else{
					$("#new_name_of_group").css('border','1px solid red');
					$("#new_name_of_group").focus(function(){
					$("#new_name_of_group").css('border','1px solid #ccc');
					});
				}
			});
			
			//отмена изменений
			$('#group_cancel_change').click(function(){
				$('#group_change_window').hide();
			});
		
		//удаление группы
		function remove_group(group,eq){
			var dialog_window=$("<div class='flash_dialog'><?= $teacher ?>, Вы действительно хотите удалить группу '"+group+"'?</div>");
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
					url:'../server/delete_group.php',
					type:'post',
					data:'id='+group,
					dataType:'text',
					success:function(result){
						$('.flash_dialog').remove();
						$('.g_table_change').eq(eq).parent().remove();
					}
				});
			});
		}
		
		//cоздание группы
		$('#create_new_group_button').click(function(){
			var new_group_name=$("#new_group_name").val();
			var new_group_special=$("#new_group_special").val();
			var new_group_course=$("#new_group_course").val();
			
			if (new_group_name.length>1){
				var json_data={
					name:new_group_name,
					special:new_group_special,
					course:new_group_course
				};
				ajax_loading('#create_new_group_button');
				$.ajax({
					url:"../server/create_new_group.php",
					type:"POST",
					data:"json_data="+$.toJSON(json_data),
					dataType:"text",
					success:function(result){
						console.log(result);
						$('#white_shadow').remove();
						if (result=="OK"){
							ajax_loading('#content');
							$("#content").load("groups.php");
						}
						else if (result=="ERROR"){
							alert('Группа с такой специальностью и курсом или именем уже существует. Измените данные параметры.');
						}
						else{
							alert(result);
						}
						
					}
				});
			}
			else{
				$("#new_group_name").css('border','1px solid red');
				$("#new_group_name").focus(function(){
					$("#new_group_name").css('border','1px solid #ccc');
				});
			}
			
		});
		
		//переключение между созданием и изменением специальности
		$('#radio1').focus(function(){
			$("#special_change_block").hide(0,function(){
				$("#special_create_block").show();
			});
		});
		$('#radio2').focus(function(){
			$("#special_create_block").hide(0,function(){
				$("#special_change_block").show();
			});
		});
		
		//создание специальности
		$("#create_new_special").click(function(){
			var input_1=false;
			var input_2=false;
			if ($("#new_speclial_name").val().length<2){
				$("#new_speclial_name").css('border','1px solid red');
				$("#new_speclial_name").click(function(){
					$(this).css('border','1px solid #ccc');
				});
			}
			else
				input_1=true;
				
			if ($("#new_special_code").val().length<1){
				$("#new_special_code").css('border','1px solid red');
				$("#new_special_code").click(function(){
					$(this).css('border','1px solid #ccc');
				});
			}
			else
				input_2=true;
			
			
			if (input_1==true && input_2==true){
				var json_data={
					code:$("#new_special_code").val(),
					name:$("#new_speclial_name").val()
				};
				ajax_loading('#create_new_special');
				$.ajax({
					url:"../server/specials.php?req=create",
					data:"json_data="+JSON.stringify(json_data),
					dataType:"text",
					type:"POST",
					success:function(res){
						$('#white_shadow').remove();
						if (res=='OK'){
							ajax_loading('#content');
							$("#content").load("groups.php")
						}
						else if (res=="ERROR")
							alert('Такие имя или код уже существуют. Измените параметры')
						else
							alert("Произошла ошибка при создании специальности. Проверьте данные и попробуйте снова!"+res);
					}
				});
			}
		});
		
		//изменение имени специальности
		$("#change_new_special").click(function(){
			if ($('#new_name_of_special').val().length>2){
				var json_data={
					special:$('#change_special_select').val(),
					new_name:$('#new_name_of_special').val()
				};
				ajax_loading('#change_new_special');
				$.ajax({
					url:"../server/specials.php?req=change",
					dataType:"text",
					type:"POST",
					data:"json_data="+$.toJSON(json_data),
					success:function(res){
						$('#white_shadow').remove();
						if (res=='OK'){
							ajax_loading('#content');
							$("#content").load("groups.php")
						}
						else if (res=="ERROR")
							alert('Такое имя специальности уже существует. Измените данные и попробуйте снова.')
						else
							alert("Произошла ошибка при создании специальности. Проверьте данные и попробуйте снова!");
					}
				});
			}
			else{
				$("#new_name_of_special").css('border','1px solid red');
				$("#new_name_of_special").click(function(){
					$(this).css('border','1px solid #ccc');
				});
			}
			
		});
		//удаление специальности
		$('#delete_special').click(function(){
				var del_special=$('#change_special_select').val();
				var del_special_name=$('#change_special_select option[value="'+del_special+'"]').text();
				var dialog_window=$("<div class='flash_dialog'><?= $teacher ?>, Вы действительно хотите удалить специальность '"+del_special_name+"'?</div>");
				var for_buttons=dialog_window.append('<div></div>');
				for_buttons.append("<button class='dialog_buttons_yes'>удалить</button>");
				for_buttons.append("<button class='dialog_buttons_no'>отмена</button>");
				dialog_window.appendTo("#containter");
				
				$('.flash_dialog .dialog_buttons_no').click(function(){
					$(".flash_dialog").remove();
				});
				
				$('.flash_dialog .dialog_buttons_yes').click(function(){
				ajax_loading('.flash_dialog');
					var json_data={
						special:del_special
					};
					$.ajax({
						url:"../server/specials.php?req=delete",
						dataType:"text",
						type:"POST",
						data:"json_data="+$.toJSON(json_data),
						success:function(res){
							if (res=="OK"){
									ajax_loading('#content');
									$("#content").load("groups.php")
									$('.flash_dialog').remove();
								}
							else if (res=="ERROR"){
								alert("Одна или несколько групп имеют эту специальность. Удаление невозможно.");
								$('.flash_dialog').remove();
							}	
							else{
								alert("При удалении произошла ошибка. Пожалуйста, обновите страницу и попробуйте снова.");
								$('.flash_dialog').remove();
							}
						}
					});
				});	
		});
	</script>