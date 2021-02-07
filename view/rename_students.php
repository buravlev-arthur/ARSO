<?php 
	session_start();
	include('../server/mysql_connect.php');
	mysqli_query("set names utf8");
	header('Content-type: text/html; charset=utf-8');
	$id=$_GET["id"];
	
	//получаем данные о изменяемом студенте
	$sql="SELECT * FROM students WHERE id=$id";
	$result=mysqli_query($connect,$sql) or die (mysqli_error($connect));
	$student=mysqli_fetch_assoc($result);
	
	
?>
	
	<script>$.getScript('../js/inputs.php');</script>
	<h1 id='objects_ul_header' class='big_header' style='margin-left:270px;margin-bottom:-10px;'>Изменение данных о студенте</h1>

<div id='new_stud' class='new_stud_main_form'>
	<h1 class="flash_window_h" style='font-size:17px;'>Данные о студенте</h1>
	<div class='new_stud_firstname'>
		<input class="basic_text" type="text" placeholder="фамилия студента" value="<?=  $student['first_name'] ?>">
		<label class='label_for_form_under'>Фамилия</label>
	</div>
	
	<div class='new_stud_lastname'>
		<input class="basic_text" type="text" placeholder="имя студента" value="<?=  $student['last_name'] ?>">
		<label class='label_for_form_under'>Имя</label>
	</div>
	
	<div class='new_stud_forfather'> 
		<input class="basic_text" type="text" placeholder="отчество студента" value="<?=  $student['for_father'] ?>">
		<label class='label_for_form_under'>Отчество</label>
	</div>
	
	<div class='new_stud_date'>
		<input class="input_date" type="text" placeholder="дата" value="<?=  Date("d.m.Y",$student['burn_date']) ?>">
		<label class='label_for_form_under'>Дата рождения</label>
	</div>
	<div class='new_stud_pol'>
		<select class='basic_select'>
			<option value='0'>Пол студента</option>
			<option value='1'>Мужской</option>
			<option value='2'>Женский</option>
		</select>
		<script>
			$(".new_stud_pol select option[value=<?= $student['male'] ?>]").attr("selected","selected");
		</script>
		<label class='label_for_form_under'>Пол</label>
	</div>
	<div class='new_stud_special'>
		<select class='basic_select'>
			<option value='0'>Специальность</option>
			<?php 
				$sql_specials="SELECT * FROM specials ORDER BY id";
				$result_specials=mysqli_query($connect,$sql_specials);
				while ($row=mysqli_fetch_assoc($result_specials))
				echo "<option value='$row[id]'>$row[id] - $row[name]</option>";
			?>
		</select>
		<script>
			$(".new_stud_special select option[value=<?= $student['special'] ?>]").attr("selected","selected");
		</script>
		<label class='label_for_form_under'>Специальность</label>
	</div>
	<div class='new_stud_year'>
		<input class="basic_text" type="text" placeholder="год поступления" value="<?=  $student['start_year'] ?>">
		<label class='label_for_form_under'>Год поступления</label>
	</div>
	<div class='new_stud_vocation'>
		<input class="basic_text" type="text" placeholder="количество лет" maxlength='1' value="<?=  $student['vocation'] ?>">
		<label class='label_for_form_under'>Отпуск</label>
	</div>
	<button id='save_new_student' class='basic_button'>сохранить</button>
</div>
<div class='flash_dialog' style='width:400px;'>
	<?= $_SESSION['user_name'] ?>, данные о студенте успешно изменены. Хотите еще внести изменения?<div></div>
	<button class="dialog_buttons_yes" style='width:160px;'>внести изменения</button>
	<button class="dialog_buttons_no" style='width:160px;'>вернуться к списку</button>
</div>
<script>
	$(".flash_dialog").hide();
	//сохранение данных о студенте
	$('#save_new_student').click(function(){
		var json_data={
			first_name:$(".new_stud_firstname input").val(),
			last_name:$(".new_stud_lastname input").val(),
			for_father:$(".new_stud_forfather input").val(),
			burn_date:$(".new_stud_date input").val(),
			male:$('.new_stud_pol select').val(),
			special:$('.new_stud_special select').val(),
			start_year:$(".new_stud_year input").val(),
			vocation:$(".new_stud_vocation input").val()
		};
		//проверка заполнения форм
		var empty_form=false;
		$('#new_stud [type=text]').each(function(){
		if(!$(this).val().length){
			empty_form=true;
			$(this).css('border', '1px solid red');
			$(this).focus(function(){
				$(this).css('border', '1px solid #489EEE');
			});
			$(this).focusout(function(){
				$(this).css('border', '1px solid #ccc');
			});
		}
		});
		
		$('#new_stud select').each(function(){
			if ($(this).val()=='0'){
				empty_form=true;
				$(this).css('border', '1px solid red');
				$(this).focus(function(){
					$(this).css('border', '1px solid #489EEE');
				});
				$(this).focusout(function(){
					$(this).css('border', '1px solid #ccc');
				});
			}
		});
		//отдача данных серверу
		if (!empty_form){
			$.ajax({
				url:'../server/rename_student.php?id=<?= $id ?>', 
				type:'POST', 
				data:'jsonData=' + $.toJSON(json_data), 
				dataType:"text",
				success: function(res){
					if (res=="OK"){
						$(".flash_dialog").show();
					}
					else if (res=="ERROR")
						alert("Извините, но в системе уже есть другой студент с такими данными")
					else{
						alert("При изменении данных о студенте произошла ошибка. Проверьте данные формы "+res);
					}
				} 
				
			});
		}

	});
	//возврат к списку студентов
	$(".dialog_buttons_no").click(function(){
		$("#content").load("students.php");
	});
	//продолжить изменять данные
	$(".dialog_buttons_yes").click(function(){
		$(".flash_dialog").hide();
	});
</script>
<?php mysqli_close($connect); ?>