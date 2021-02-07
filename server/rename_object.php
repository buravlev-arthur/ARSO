<?php 
include ('mysql_connect.php');
$data=json_decode($_POST['jsonData']);
$id=$data->id;
$val=$data->val;
$sql="UPDATE objects SET name='$val' WHERE id=$id";
mysqli_query($connect,$sql) or die (mysqli_error($connect));
echo $data->val;
mysqli_close($connect);
?>