<?php
	session_start();
	header('Content-type: text/html; charset=utf-8');
	include('mysql_connect.php');
	mysqli_query($connect,"set names utf8");
	$data = json_decode($_POST['jsonData']);
	$login=$data->login;
	$password=md5($data->password);
	$sql="SELECT id, last_name, for_father FROM teachers WHERE login='$login' and password='$password'";
	$result=mysqli_query($connect,$sql) or die (mysqli_error($connect));
	$row=mysqli_fetch_assoc($result);
	if (isset($row['id'])){
		$name=$row['last_name'];
		$oth=$row['for_father'];
		$_SESSION['user_name']=$name.' '.$oth;
		$_SESSION['user_id']=$row['id'];
		echo "1";
	}
	else echo "0";
	mysqli_close($connect);
?>