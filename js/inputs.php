<?php 
session_start();
header('Content-Type: charset=utf-8');
?>
$(function(){
//switch on DataPickers
		$(".input_date").datepicker({dateFormat:"dd.mm.yy",showOn:"both",buttonImage:"../js/jquery-ui/css/smoothness/images/calendar.gif",buttonImageOnly:true,dayNamesMin: ["Вс","Пн","Вт","Ср","Чт","Пт","Сб"],
		monthNames:["Январь","Февраль","Март","Апрель","Май","Июнь","Июль","Август","Сентябрь","Октябрь","Ноябрь","Декабрь"],
		changeMonth: true,
		changeYear: true,
		yearRange: "1980:2020"});
//up_dialogs
		$(".up_dialogs").dialog({resizable: false});
//buttons
$( "button[class=basic_button]").button();
//radios
$(".basic_radios").buttonset();
});