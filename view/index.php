<?php
session_start();
//error_reporting(E_ERROR);
header('Content-type: text/html; charset=utf-8');
if (!isset($_SESSION['user_name']) or $_SESSION['user_name']=='')
	header('Location: enter.php');
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset='utf-8'>
		<script src='../js/jquery.js'></script>
		<script src='../js/jquery-ui/js/jquery-ui.js'></script>
		<script src="../js/jquery.json-2.4.min.js"></script>
		<link rel="stylesheet" href="../js/jquery-ui/css/smoothness/jquery-ui.css" />
		<!--For scrolling-->
			<link type="text/css" rel="stylesheet" href="../css/jquery.jscrollpane.css"/>
			<script type="text/javascript" src="../js/jquery.mousewheel.js"></script>
			<script type="text/javascript" src="../js/jquery.jscrollpane.js"></script>
		<!--Scrolling end-->
		
		<!--for graphics-->
			<script language="javascript" type="text/javascript" src="../js/jqplot/jquery.jqplot.min.js"></script>
			<script type="text/javascript" src="../js/jqplot/plugins/jqplot.dateAxisRenderer.js"></script>
			<script type="text/javascript" src="../js/jqplot/plugins/jqplot.canvasTextRenderer.min.js"></script>
			<script type="text/javascript" src="../js/jqplot/plugins/jqplot.canvasAxisLabelRenderer.min.js"></script>
			<script type="text/javascript" src="../js/jqplot/plugins/jqplot.canvasAxisTickRenderer.min.js"></script>
			<script type="text/javascript" src="../js/jqplot/plugins/jqplot.categoryAxisRenderer.min.js"></script>
			<script type="text/javascript" src="../js/jqplot/plugins/jqplot.barRenderer.min.js"></script>
			<script type="text/javascript" src="../js/jqplot/plugins/jqplot.pieRenderer.min.js"></script>
			<script type="text/javascript" src="../js/jqplot/plugins/jqplot.donutRenderer.min.js"></script>
			<script type="text/javascript" src="../js/jqplot/plugins/jqplot.pointLabels.min.js"></script>
			<link rel="stylesheet" type="text/css" href="../js/jqplot/jquery.jqplot.css" />
		<!--for graphics end-->
		
		<link rel="stylesheet" href="../css/main.css" />
		<script src="../js/main.php"></script>
	</head>
	<body>
		<div id='containter'>
				<div id='head_menu'>
                    <div id='head_menu_header'>
                        <div id='head_menu_button'></div>
                        МЕНЮ СИСТЕМЫ
                    </div>
					<div class='head_menu_button' onclick="ajax_loading('#content');$('#content').load('organaizer.php?time=<?= time() ?>');">
                        <div id='hm_icon_organaizer' class='head_menu_icon'></div>
                    Органайзер</div>
					<div class='head_menu_button head_menu_button_active' onclick="ajax_loading('#content');$('#content').load('objects_of_teacher.php');">
                        <div id='hm_icon_objects' class='head_menu_icon'></div>
                    Занятия</div>
					<?php 
						if (Date('m')>8)
							$semestr=1;
						else
							$semestr=2;
					?>
					<div class='head_menu_button' onclick="ajax_loading('#content');$('#content').load('statistic.php?year=<?= Date('Y') ?>&sem=<?= $semestr ?>&obj=0&gr=0');">
                        <div id='hm_icon_statistic' class='head_menu_icon'></div>
                    Статистика</div>
					<div class='head_menu_button' onclick="ajax_loading('#content');$('#content').load('students.php');">
                        <div id='hm_icon_students' class='head_menu_icon'></div>    
                    Студенты</div>
					<div class='head_menu_button' onclick="ajax_loading('#content');$('#content').load('groups.php');">
                        <div id='hm_icon_groups' class='head_menu_icon'></div>    
                    Группы</div>
					<div class='head_menu_button'>
                        <div id='hm_icon_teachers' class='head_menu_icon'></div>    
                    Преподаватели</div>
				</div>
			<div id='head'>
				<div id='logo_min'></div>
                <h1 id='header_of_system'>Автоматизированная рейтинговая система оценивания успеваемости учащихся</h1>

				<div id='head_user_info'>
                    <div id='user_avatar'></div>
				    <div id='user_name'><?= $_SESSION['user_name'] ?></div>
				    <div id='user_options'>
					   <a id='user_options_options'>настройки</a> | <a id='user_options_exit'>выход</a>
				    </div>
                </div>
			</div>
			<div id='content'>
			</div>
		</div>
	</body>
</html>