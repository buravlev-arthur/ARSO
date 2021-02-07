<?php 
	session_start();
	include('../server/mysql_connect.php');
	mysqli_query($connect,"set names utf8");
	header('Content-type: text/html; charset=utf-8');
	$teacher=$_SESSION['user_name'];
	$teacher_id=$_SESSION['user_id'];
	
?>
<h1 id='objects_ul_header' class='big_header'>Список студентов</h1>
<button id="create_students">добавить студента +</button>
<!--<button id="change_students">изменить</button>
<button id="delete_students">удалить</button>-->
<label for="search_students" id="search_st_label">Поиск:<input autofocus placeholder="Начните ввод..." id="search_students" type="type"><button id="start_search_stud_button">искать</button></label>

<table cellspacing='0' id='students'>
	<tr>
		<th><input type='checkbox' id='check_all_st' ></th>
		<th id='sort_first_name' class='sortible'>Фамилия</th>
		<th id='sort_last_name' class='sortible'>Имя</th>
		<th id='sort_for_father' class='sortible'>Отчество</th>
		<th id='sort_course,special' class='sortible'>Группа</th>
		<th id='sort_start_year' class='sortible'>Год поступления</th>
		<th id='sort_vocation' class='sortible'>Отпуск</th>
		<th id='sort_burn_date' class='sortible'>Дата рождения</th>
		<th style='border-right:none;'>Редактировать</th>
	</tr>
	<!--В ДРУГОЙ ФАЙЛ!-->
	<?php
		//получим массив имен специальностей
		$sql_specials="SELECT * FROM specials";
		$result_specials=mysqli_query($connect,$sql_specials);
		$specials_names=Array();
		while ($row_specials=mysqli_fetch_assoc($result_specials))
			$specials_names[$row_specials['id']]=$row_specials['name'];
	
		if ($_GET['sort_by']!="")
			$sort_by=$_GET['sort_by'];
		else
			$sort_by='first_name';
			if (isset($_GET['search'])){
				$search=$_GET['search'];
				if (Date("m")<9)
					$sql="SELECT *, ".Date("Y")."-(start_year+vocation) as course FROM students WHERE first_name LIKE '%$search%' OR last_name LIKE '%$search%' OR for_father LIKE '%$search%' OR start_year LIKE '%$search%' ORDER BY $sort_by";
				else
					$sql="SELECT *, (".Date("Y")."-(start_year+vocation))+1 as course FROM 	students WHERE first_name LIKE '%$search%' OR last_name LIKE '%$search%' OR for_father LIKE '%$search%' OR start_year LIKE '%$search%' ORDER BY $sort_by";
			}
			else{
				if (Date("m")<9)
					$sql="SELECT *, ".Date("Y")."-(start_year+vocation) as course FROM students ORDER BY $sort_by";
				else
					$sql="SELECT *, (".Date("Y")."-(start_year+vocation))+1 as course FROM 	students ORDER BY $sort_by";
			}
			
		$result=mysqli_query($connect,$sql) or die(mysqli_error($connect));
		$prevent_sort_value=" ";
		$student_count=0;
		while ($row=mysqli_fetch_assoc($result)){
			$student_count++;
			if ($sort_by!="start_year" and $sort_by!="burn_date" and $sort_by!="group" and $sort_by!="vocation" and $sort_by!='course,special'){
				if (substr($prevent_sort_value,0,2)!=substr($row[$sort_by],0,2)){
					echo "<tr style='background:#FFF8DF;'><td colspan='9' style='text-align:left;padding:1px;padding-left:8px;font-size:16px;font-weight:bold;color:#757534'>".substr($row[$sort_by],0,2)."</td></tr>";
					$prevent_sort_value=$row[$sort_by];
				}
			}
			
			else if ($sort_by=="start_year"){
				if ($prevent_sort_value!=$row[$sort_by]){
					echo "<tr style='background:#FFF8DF;'><td colspan='9' style='text-align:left;padding:1px;padding-left:8px;font-size:16px;font-weight:bold;color:#757534'>".$row[$sort_by]." год</td></tr>";
					$prevent_sort_value=$row[$sort_by];
				}
			}
			
			else if ($sort_by=="vocation"){
				if ($prevent_sort_value!=$row[$sort_by]){
					echo "<tr style='background:#FFF8DF;'><td colspan='9' style='text-align:left;padding:1px;padding-left:8px;font-size:16px;font-weight:bold;color:#757534'>".$row[$sort_by]." год/лет</td></tr>";
					$prevent_sort_value=$row[$sort_by];
				}
			}
			
			else if ($sort_by=="burn_date"){
				if ($prevent_sort_value!=Date("Y",$row[$sort_by])){
					echo "<tr style='background:#FFF8DF;'><td colspan='9' style='text-align:left;padding:1px;padding-left:8px;font-size:16px;font-weight:bold;color:#757534'>".Date("Y",$row[$sort_by])." год</td></tr>";
					$prevent_sort_value=Date("Y",$row[$sort_by]);
				}
			}
			else if ($sort_by=="course,special"){
				if ($prevent_sort_value!=$row['course'].$row['special']){
					echo "<tr style='background:#FFF8DF;'><td colspan='9' style='text-align:left;padding:1px;padding-left:8px;font-size:16px;font-weight:bold;color:#757534'>".$row['course']." курс /  ".$specials_names[$row['special']]."</td></tr>";
					$prevent_sort_value=$row['course'].$row['special'];
				}
			}
			
			echo "<tr class='selecteble'>";
				echo "<td class='td_for_check'><input type='checkbox' value='$row[id]' class='checking_students'></td>";
				echo "<td class='student_names'>$row[first_name]</td>";
				echo "<td class='student_names'>$row[last_name]</td>";
				echo "<td class='student_names'>$row[for_father]</td>";
				echo "<td class='table_st_groups'>";
					//определить на каком курсе сейчас студент
					if (Date('m')<9){
						$course=Date('Y')-($row['start_year']+$row['vocation']);
					}
					else{
						$course=(Date('Y')-($row['start_year']+$row['vocation']))+1;
					}
					$sql_group="SELECT id FROM groups_structure WHERE course=$course AND special=$row[special]";
					$group_st_result=mysqli_query($connect,$sql_group);
					if (mysqli_num_rows($group_st_result)!=0){
						$group_st_row=mysqli_fetch_assoc($connect,$group_st_result);
						echo $group_st_row["id"];
					}
					else
						echo "не определена";
				echo "</td>";
				echo "<td>$row[start_year]</td>";
				echo "<td>$row[vocation] год/лет</td>";
				echo "<td>".Date("d.m.Y",$row[burn_date])."</td>";
				echo "<td class='change_student' style='border-right:none;'><span class='redact_student'>изменить</span><span style='color:#aaa;font-size:10px;'>/ </span><span class='del_student' style='font-weight:normal;'>удалить</span></td>";
			echo "</tr>";
		}
	?>
</table>
<?php
	if ($student_count==2 or $student_count==3 or $student_count==4)
		echo "<div id='count_of_student'>Всего найдено $student_count студента</div>";
	else if ($student_count==1)
		echo "<div id='count_of_student'>Найден $student_count студент</div>";
	else
		echo "<div id='count_of_student'>Всего найдено $student_count студентов</div>";
?>
<script>
	//закрашиваем заголовок таблицы, по которому сортируем
		$("#students th[id='sort_<?= $_GET['sort_by'] ?>']").css({
			'background':'#0049eb',
			'color':'#fff'
		});
	//выбор студентов с помощью checkbox
	var checked_studs=new Array();
	$(".checking_students").click(function(){
		if ($(this).prop("checked")){
			b=false;
			for (i=0;i<checked_studs.length;i++){
				if (checked_studs[i]==-1){
						checked_studs[i]=$(this).val();
						b=true;
						break;
				}
			}
		if (!b)
			checked_studs[checked_studs.length]=$(this).val();
			$(this).parent().parent().css('background','#ddd');
		}
		else{
			for (i=0;i<checked_studs.length;i++){
				if (checked_studs[i]==$(this).val())
					checked_studs[i]=-1;
			}
			$(this).parent().parent().css('background','#fff');
		}
	});
	
	//использование общего checkbox
	$("#check_all_st").click(function(){
		if ($(this).prop("checked")){
			$(".checking_students").prop("checked","checked");
			$("table#students tr.selecteble").css('background','#ddd');
			for (i=0;i<$(".checking_students").length;i++){
				checked_studs[i]=$(".checking_students").eq(i).val();
			}
		}
		else{
			$(".checking_students").prop("checked","");
			$("table#students tr.selecteble").css('background','#fff');
			for (i=0;i<$(".checking_students").length;i++){
				checked_studs[i]=-1;
			}
		}
	});
	
	//удаление студентов (студента)
	$("#delete_students").click(function(){
		var str="";
		for (i=0;i<checked_studs.length;i++){
			str+=checked_studs[i]+"  ";
		}
		alert(str);
	});
	//добавление студентов
	$("#create_students").click(function(){
		ajax_loading('#content');
		$("#content").load("new_students.php");
	});
	//редактирование студента
	$(".redact_student").click(function(){
		var id_student=$(this).parent().parent().find("[type='checkbox']").val();
		ajax_loading('#content');
		$("#content").load("rename_students.php?id="+id_student);
	});
	//удаление студента
	$(".del_student").click(function(){
		var id_student=$(this).parent().parent().find("[type='checkbox']").val();
		var tr_del=$(this).parent().parent();
		var dialog_window=$("<div class='flash_dialog'><?= $teacher ?>, Вы действительно хотите удалить данного студента из системы?</div>");
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
					url:'../server/delete_student.php',
					type:'post',
					data:'id='+id_student,
					dataType:'text',
					success:function(result){
						$('.flash_dialog').remove();
						tr_del.remove();
					}
				});
			});
	});
	
	//переключение сортировки
		$("#students th.sortible").click(function(){
			var str=$(this).attr('id');
			switch (str){
				case 'sort_first_name': ajax_loading('#content'); $("#content").load("students.php?sort_by=first_name"); break;
				case 'sort_last_name': ajax_loading('#content'); $("#content").load("students.php?sort_by=last_name"); break;
				case 'sort_for_father': ajax_loading('#content'); $("#content").load("students.php?sort_by=for_father"); break;
				case 'sort_burn_date': ajax_loading('#content'); $("#content").load("students.php?sort_by=burn_date"); break;
				case 'sort_course,special': ajax_loading('#content'); $("#content").load("students.php?sort_by=course,special"); break;
				case 'sort_start_year': ajax_loading('#content'); $("#content").load("students.php?sort_by=start_year"); break;
				case 'sort_vocation': ajax_loading('#content'); $("#content").load("students.php?sort_by=vocation"); break;
				default:ajax_loading('#content'); $("#content").load("students.php?sort_by=first_name");
			}
		});
		
		//сортировочный поиск
		$('#search_students').keydown(function(event){
						if (event.which==13)
							go_to_search_students();
		});
		$('#start_search_stud_button').click(function(){
			go_to_search_students();
		});
		
		function go_to_search_students(){
			if ($("#search_students").val()!="" && $("#search_students").val()!=" "){
				ajax_loading('#content');
				$("#content").load("students.php?sort_by=first_name&search="+$("#search_students").val());
			}
		}
</script>
<?php 	mysqli_close($connect); ?>