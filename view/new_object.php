<?php
session_start();
header('Content-type: charset=utf-8');
include('../server/mysql_connect.php');
mysqli_query($connect,"set names utf8");
?>				
<script>
				$.getScript('../js/inputs.php');
				//удаляем предыдущий обработчик событий live
				$(".under_groups").die("change");
				$(".under_groups").die("click");
				$('.group_for_object').die('click');
				$('.save_undergroups').die('click');
				$('.ug_window_open').die('click');
				$('.group_for_object').die('change');
				
                var cr_obj_step = 1; //индикатор страницы создания предмета
                
                //выполнение проверки заполнения форм и переход к следующему шагу
                /*$('#new_object_next').click(function(){
                    if (cr_obj_step==1){
                        
                        if ($('#new_object_name').val().length<2){
                            alert('Вы не ввели название предмета.')
                            return 0;
                        };
                        if ($('#new_object_year').val().length<4){
                            alert('Вы не указали год преподавания предмета.')
                            return 0;
                        };
                        if ($('#group_1 .group_for_object').val()=='0'){
                            alert('Вы не выбрали ни одной группы для предмета.')
                            return 0;
                        };
                        
                        $("#create_object_window_"+cr_obj_step++).hide();
                        $("#create_object_window_"+cr_obj_step).show();
                        
                        $('.create_object_step_button.active').removeClass('active');
                        $('.create_object_step_button').eq(cr_obj_step-1).addClass('active');
                        
                        $("#create_object_step_indicator").text("Шаг "+cr_obj_step+" из 3");
                    }
                    else if (cr_obj_step==2){
                        
                        $("#create_object_window_"+cr_obj_step++).hide();
                        $("#create_object_window_"+cr_obj_step).show();
                        
                        $('.create_object_step_button.active').removeClass('active');
                        $('.create_object_step_button').eq(cr_obj_step-1).addClass('active');
                        
                        $("#create_object_step_indicator").text("Шаг "+cr_obj_step+" из 3");
                        
                        $('#new_object_next').hide();
                        $('#new_object_go').show();
                    }
                });*/
    
                //Создать предмет (выполнено все 3 шага)
				$('#new_object_go').click(function(){
					//массив групп
					var groups_massiv=new Array();
					var undergroups_massiv=new Array();
					
					if ($('#new_object_name').val().length<2){
						alert('Вы не ввели название предмета.')
						return 0;
					};
					if ($('#new_object_year').val().length<4){
						alert('Вы не указали год преподавания предмета.')
						return 0;
					};
					if ($('#group_1 .group_for_object').val()=='0'){
						alert('Вы не выбрали ни одной группы для предмета.')
						return 0;
					};
                    
					//заполняем массивы групп и подгрупп для отправки
					for (i=0;i<groups_count;i++){
						groups_massiv[i]=new Array();
						undergroups_massiv[i]=new Array;
						//данные о названии групп и количестве подгрупп
						if ($('#group_'+(i+1)+' .group_for_object').val()!="0"){
							groups_massiv[i][0]=$('#group_'+(i+1)+' .group_for_object').val();
							groups_massiv[i][1]=$('#group_'+(i+1)+' .under_groups').val();
						}
						//данные о распределении подгрупп
						if (groups_massiv[i][1]>1){
							for (j=0;j<groups_massiv[i][1];j++){
								for (el=0;el<$("#"+groups_massiv[i][0]+" .section_"+(j+1)+" .undergroup_student_ul").length;el++){
									undergroups_massiv[i][$("#"+groups_massiv[i][0]+" .section_"+(j+1)+" .undergroup_student_ul").eq(el).attr('id')]=j+1;
								}
							}
						}
						else
						undergroups_massiv[i][0]=0;	
					}
					
					
					//формируем объект с данными для отправки
					var no_go={
						'name':$('#new_object_name').val(),
						'year':$('#new_object_year').val(),
						'part_of_year':$('#object_session_number .ui-state-active span').text(),
						'groups':groups_massiv,
						'undergroups':undergroups_massiv,
						'teacher': '<?= $_SESSION["user_id"] ?>'
					};
					ajax_loading('#new_object');
					 $.ajax({
							url:'../server/create_new_object.php', 
							type:'POST', 
							data:'jsonData=' + $.toJSON(no_go), 
							dataType:"text",
							success: function(res) {
								if (res!='no_students'){
								
								$('#white_shadow').remove();
								ajax_loading('#content');
								$('#content').load('lessons_of_object.php?lesson='+res);
								}
								else if (res=='no_students'){
									$('#white_shadow').remove();
									alert('Ещё нет студентов для данного предмета на этот год в одной из групп.');
								}
							}
						});
				});
				
				//проверка на совпадение групп
				$('.group_for_object').live('change',function(){
					var there=$(this).index('.group_for_object');
					for (i=0;i<$('.group_for_object').length;i++){
						if (i!=there){
							if ($('.group_for_object').eq(i).val()==$(this).val()){
								alert($(this).val()+' группа уже была выбрана ранее');
								$(this).find('option[value="0"]').attr('selected','selected');
							}
						}
					}
					if ($("#"+prepend_group).length>0){
						$("#"+prepend_group).remove();
						$(this).parent().find('.under_groups option[value="1"]').attr('selected','selected');
					}
				});
				//проверка на изменение группы и удаление окна подгрупп
				var prepend_group="";
				$('.group_for_object').live('click',function(){
					prepend_group=$(this).val();
				});
				//добавить выбор группы
				var groups_count=1;
				$('#add_group_for_object').click(function(){
					for (i=0;i<$('.group_for_object').length;i++){
						if ($('.group_for_object').eq(i).val()=="0"){
							alert("В предыдущем поле еще не выбрана группа.");
							return 0;
						}
					}
					$('#group_'+groups_count).clone().attr('id','group_'+(++groups_count)).insertAfter('#group_'+(groups_count-1));
                    $('#group_'+groups_count+" .groups_of_creating_object_headers").text(groups_count+" группа");
				});
				

				//событие при выборе количества подгрупп
				$(".under_groups").live('change',function(e){
					var group_container=$(this).parent().parent().parent();
					var the_group=group_container.find('.group_for_object').val();
					if ($(this).val()!="1"){
						if (group_container.find('.group_for_object').val()=="0"){
							alert('Сначала нужно выбрать группу');
							$(this).find('option[value="1"]').attr('selected','selected');
						}
						else{
							if ($("#"+the_group).length==1)
								$("#"+the_group).remove();
								create_group_section(the_group,$(this).val(),$(this).index('option'));
							
						}
					}
					else{
						if ($("#"+the_group).length==1)
							$("#"+the_group).remove();
					}
					return false;
				});
				

				
				//заполнение списка студентов для подгрупп
				function create_group_section(the_group,sections,option_index){
					var object_year=$('#new_object_year').val();
					if ($('#radio1').attr('checked')=='checked')
						var object_year_part='1'
					else
						var object_year_part='2';
					if (object_year==""){
						alert('Пожалуйста, укажите год преподавания предмета.');
						$("#white_shadow").remove();
					}
					else{
						$.ajax({
							url:'../server/select_students_of_group.php?group='+the_group+'&year='+object_year+'&part='+object_year_part, 
							type:'GET', 
							dataType:"text",
							success: function(res) {
								$("#white_shadow").remove();
								if (res=="ERROR"){
									alert('Студентов в '+the_group+' группе еще нет.');
									$("option").eq(option_index).parent().parent().find('.under_groups option[value="1"]').attr('selected','selected');	
								}
								else{
									
										//формируем окно для перетаскивания студентов в подгруппы
                                        $('.undergroup_student_ul').die('click');
										var ug_block=$("<div id='"+the_group+"' class='undergroup_block'></div>");
										ug_block.appendTo("#content");
										ug_block.css({
											'width':234*sections+"px",
											'margin-left':-243*sections/2+"px"
										});
										$("<h1 class='flash_window_h'>Распределение студентов по подгруппам в "+the_group+" группе</h1>").appendTo(ug_block);
										for (i=1;i<=sections;i++){
											$("<div class='undergroup_sections_header'>Подгруппа "+i+"</div>").appendTo(ug_block);
										}
										for (i=1;i<=sections;i++){
											$("<div class='section_"+i+" undergroup_sections'></div>").appendTo(ug_block);
										}
										$("<button class='save_undergroups basic_button'>сохранить</button>").appendTo(ug_block);
										$(ug_block).find('.section_1').html(res);
                                    
                                        //перемещение студентов в другие подгруппы
                                        var student;
                                        $('.undergroup_student_ul').live('click',function(){
                                            //если две подгруппы
                                            if ($('.undergroup_sections').length==2){
                                                
                                                if ($(this).parent().hasClass("section_1")){
                                                    $(this).appendTo('.section_2');
                                                }
                                                else{
                                                    $(this).appendTo('.section_1');
                                                }
                                            }
                                            else{
                                                
                                                 $('.undergroup_student_ul').css({
                                                    'background':'none',
                                                    'color':'#444'
                                                 });
                                                $('.undergroup_student_ul').removeClass('active');
                                                
                                                student = $(this);
                                                student.css({
                                                    'background':'#3364c3',
                                                    'color':'#fff'
                                                });
                                                student.addClass('active');
                                                

                                            }
                                            
                                        });
                                                //событие клика по ячейке подгруппы
                                                /*$('.undergroup_sections').click(function(){
														console.log(student);
                                                        if ($(this).index('.undergroup_sections') != student.parent().index('.undergroup_sections') && student.hasClass('active')){
                                                            student.appendTo(this);
                                                            student.css({
                                                                'background':'none',
                                                                'color':'#444'  
                                                            });
                                                            student.removeClass('active');
                                                        }
                                                        else{
                                                            student.removeClass('active');
                                                            student.css({
                                                                'background':'none',
                                                                'color':'#444'  
                                                            });
                                                        }
                                                });*/
										
										//draganddrop для студентов в подгруппах
										/*$(ug_block).find(".undergroup_student_ul" ).draggable();
										
										
										$(ug_block).find(".undergroup_sections").droppable({
											drop: function( event, ui ) {
												$("<div class='undergroup_student_ul'></div>").attr('id',ui.draggable.attr('id')).text(ui.draggable.text()).draggable().appendTo(this);
												ui.draggable.remove();
											
											}
										});	*/
										$(ug_block).show();
									
								}
							}
						});
					}
				}
				
				
				//cохранение массивов с подгруппами
				$('.save_undergroups').live('click',function(){
					var empty_flag=false;
					$(this).parent().find('.undergroup_sections').each(function(){
						if ($(this).html()==""){
							empty_flag=true;
						}
					});
					if (empty_flag){
						alert("В каждой погруппе должен быть хотя бы один студент.");
						return 0;
					}
					else
						$(this).parent().hide();
				});
				
				//открыть окно с подгруппами
				$('.ug_window_open').live('click',function(){
					if ($(this).parent().find('.under_groups').val()!=1)
						$("#"+$(this).parent().find('.group_for_object').val()).show();
				});
				
				//проверка на изначальное указание года перед выбором количества подгруп
				$('.under_groups').live('click',function(){
					if ($('#new_object_year').val().length<4){
						alert("Вы не указали год преподавания предмета");
					}
				});
                
                //события открытия подсказок при наведении на кнопки подсказок
                $('.input_helper_button').live('mouseover',function(){
                    $(this).find('.input_helper_text').show();
                });
                //события закрытия подсказок при снятии наведения на кнопки подсказок
                $('.input_helper_button').live('mouseleave',function(){
                    $(this).find('.input_helper_text').hide();
                });
	</script>
				
				<h1 class='big_header' id='new_object_block_header'>Создание нового предмета</h1>
				<div id='new_object' class='center_grey_block'>
				<!--<div id='create_object_steps_block'>
                    <div class='create_object_step_button active' id='create_object_step1_button'>
                        <div></div>
                        <b>1</b> Общие настройки
                    </div>
                    <div class='create_object_step_button' id='create_object_step2_button'>
                        <div></div>
                        <b>2</b> Виды занятий
                    </div>
                    <div class='create_object_step_button' id='create_object_step3_button'>
                        <div></div>
                        <b>3</b> Балльно-рейтинговая модель
                    </div>
                </div>-->
                <div id='create_object_window_1' class='create_object_windows'>    
                        <label class='for_basic_text' for='name_new_object'>Название предмета
                            <input id='new_object_name' class='basic_text' type='text' placeholder='введите название'></input>
                            <div class='input_helper_button'>?
                                <div class='input_helper_text'>
                                    <div></div>
                                    Напишите полное название нового предмета. Дополнительно в этом текстовом поле можно указать сокращенное название предмета в скобках.</div>
                            </div>

                        </label>

                        <label class='for_basic_text' for='new_object_year'>Учебный год
                            <input class='basic_text' id='new_object_year' type='text' placeholder='введите год'></input>
                            <div class='input_helper_button'>?
                                <div class='input_helper_text'>
                                    <div></div>
                                    Укажите учебный год в формате "ХХХХ" (например: <?= Date("Y") ?>). Учитывайте, что, например, в <?= Date("Y") ?>/<?= (Date("Y")+1) ?> учебном году зимней сессии соответствует <?= Date("Y") ?> год, а летней сессии уже <?= (Date("Y")+1) ?> год.</div>
                            </div>
                        </label>


                        <div id='object_session_number' class="basic_radios">
                            <input type="radio" id="radio1" name='object_session_number'/><label class='for_basic_text' for="radio1">Зимняя сессия</label>

                            <input type="radio" id="radio2" name='object_session_number' checked="checked" /><label class='for_basic_text' for="radio2">Летняя сессия</label>
                        </div>

                        <div id='over_groups_helper_block'>
                            <div class='input_helper_button'>?
                                <div class='input_helper_text'>
                                    <div></div>
                                   Вы можете добавить одну группу выбрав ее в списке "Группа".<br><br> Если по предмету будут обучаться несколько групп, добавьте дополнительные группы нажав на кнопку "добавить группу" внизу.<br><br> Если обучаемая группа - одна или несколько - должна быть поделена на подгруппы, вибирите в списке "подгруппы" необходимое количество подгрупп и сформируйте списки студентов в подгруппах перетаскивая студентов из списка в нужные ячейки подгрупп (по умолчанию все студенты будут находится в ячейке первой подгруппы). </div>
                            </div>
                        </div>

                        <div id='group_1' class='groups_of_creating_object'>
                            <div class='groups_of_creating_object_headers'>1 группа</div>
                            <label class='for_basic_text' for='basic_ol'>
                                <select class="basic_select group_for_object" style='float:right'>
                                    <option value='0'>выберите группу</option>
                                    <?php 
                                        $sql_groups="SELECT * FROM groups_structure ORDER BY id";
                                        $result_groups=mysqli_query($connect,$sql_groups);
                                        while ($row=mysqli_fetch_assoc($result_groups))
                                            echo "<option value='$row[id]'>$row[id]</option>";
                                    ?>
                                </select>Группа
                            </label>


                            <div class='undgr_block'>
                                <label class='for_basic_text'>Подгруппы
                                    <select class='basic_select under_groups'>
                                        <option value='1'>Не делить</option>
                                        <option value='2'>Две подгруппы</option>
                                        <option value='3'>Три подгруппы</option>
                                        <option value='4'>Четыре подгруппы</option>
                                    </select>
                                </label>
                            </div>

                            <button class='ug_window_open'>подгруппы</button>

                        </div>  

                        <div id='add_group_for_object'><span>добавить группу</span><span>v</span></div>
                    </div>





                    <div id='create_object_window_2' class='create_object_windows'>
                        Второе окно
                    </div>





                    <div id='create_object_window_3' class='create_object_windows'>
                        Третье окно
                    </div>




					<div id='create_object_buttons_block'>
                        <div id='create_object_step_indicator'>Шаг 1 из 3</div>
                        <button id='new_object_go' style="display:block;" class='basic_button'>создать предмет</button>
                        <button id='new_object_next' style="display:none;" class='basic_button'>далее</button>
                    </div>
				</div>
<?php mysqli_close($connect); ?>