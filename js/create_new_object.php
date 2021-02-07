<?php 
session_start();
header('Content-Type: charset=utf-8');
?>
//go create new object
$('#new_object_go').click(function(){
	var no_go={
		'name':$('#new_object_name').val(),
		'year':$('#new_object_year').val(),
		'part_of_year':$('#object_session_number .ui-state-active span').text(),
		'group_number':$('#new_object_group_number .ui-selected').text(),
		'teacher': '<?= $_SESSION["user_id"] ?>'
	};
	ajax_loading('new_object');
	 $.ajax({
            url:'server/create_new_object.php', 
			type:'POST', 
			data:'jsonData=' + $.toJSON(no_go), 
			dataType:"text",
			success: function(res) {
				if (res!='no_students'){
				$('#white_shadow').remove();
				$('#content').load('lessons_of_object.php?lesson='+res);
				}
				else{
					$('#white_shadow').remove();
					alert('ещё нет студентов для данного предмета на этот год. проверьте данные ещё раз');
				}
            }
        });
});
