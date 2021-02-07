<?php 
header('Content-type: text/html; charset=utf-8');
error_reporting( E_ERROR );
?>
<!DOCTYPE HTML>
<html>
	<head>
		<meta charset='utf-8'>
		<script src='../js/jquery.js'></script>
		<script src='../js/jquery-ui/js/jquery-ui.js'></script>
		<script src="../js/main.php"></script>
		<script>
			//IF IE
			if ($.browser.msie){
				alert("Ситема АРСО в браузере Internet Explorer не поддерживается. Установите современный браузер.");
				window.location.href = "https://www.google.ru/intl/ru/chrome/browser/";
			}
		</script>
		<script src="../js/jquery.json-2.4.min.js"></script>
		<link rel="stylesheet" href="../js/jquery-ui/css/smoothness/jquery-ui.css" />
		<link rel="stylesheet" href="../css/main.css" />
	</head>
	<body>
		<script>
			$.getScript('../js/inputs.php');
				var qaunter=0;
				$(function(){
					$('#go_to_enter').click(function(){
						go_to_enter();
				});
				$('body').keydown(function(event){
						if (event.which==13)
							go_to_enter();
					});
				
			});
			function go_to_enter(){
				var json = {
						'login':$('#user_login').val(),
						'password':$('#user_password').val()
					};
					ajax_loading('#enter_form');
					$.ajax({
						url:'../server/autorisation.php',
						type:'POST',
						data: 'jsonData=' + $.toJSON(json),
						dataType: "text",
						success: function(res){
							if (res==1){
								$('#white_shadow').remove();
								document.location.href = "index.php";
							}
							else {
								console.log(res);
								$('#white_shadow').remove();
								$('#user_login').css('border','1px solid red');
								$('#user_password').css('border','1px solid red');
								qaunter++;
								//доработать!!!
								if (qaunter>=5){
									alert('Много неверных попыток. Попробуйте войти через 40 минут.');
								}
							}
						}
					});
			};
		</script>
		<div id='containter'>
			<div id='enter_logo'></div>
			<div id='enter_form' class='center_grey_block'>
				<div id='enter_from_header'>Авторизация пользователя</div>
				<label class='for_basic_text' for='user_login'>Ваш логин<input id='user_login' name='user_login' class='basic_text' type='text' placeholder='введите логин'></input></label>
				<label class='for_basic_text' for='user_password'>Пароль<input class='basic_text' id='user_password' name='user_password' type='password' placeholder='введите пароль'></input></label>
				<button id='go_to_enter' class='basic_button'>войти</button>
			</div>
            <?php 
                include_once('footer_description.php');
            ?>
		</div>
	</body>
</html>