<?php
error_reporting(E_ERROR);
define(DB_SERVER,"localhost");
define(DB_USER,"u1156424_default");
define(DB_PASSWORD,"o!e!3DNm");
define(DB_NAME,"u1156424_arso");
$connect = mysqli_connect(DB_SERVER,DB_USER,DB_PASSWORD) or die ("<div id='general_error'>Невозможно установить соединение с базой данных</div>");
mysqli_select_db($connect,DB_NAME) or die ("<div id='general_error'>Нет доступка к базе данных arso</div>");
mysqli_set_charset($connect, "utf8");
?>
