<?php
	header("Content-type: text/html; charset=utf-8");
	require('mysql_connect.php');
	$s=trim($_GET['search']);
	if (substr_count($s," ")==1){
		$first_name=iconv("utf-8","cp1251",substr($s,0,strpos($s," ")));
		$last_name=iconv("utf-8","cp1251",substr($s,strpos($s," ")+1));
		$sql="SELECT id,first_name,last_name,for_father,start_year FROM students WHERE first_name='$first_name' AND last_name='$last_name'";
	}
	else if (substr_count($s," ")==0){
		$first_name=iconv("utf-8","cp1251",$s);
		$sql="SELECT id,first_name,last_name,for_father,start_year FROM students WHERE first_name='$first_name'";
	}
	else{
		echo "ERROR";
		exit();
	}
		$result=mysqli_query($connect,$sql) or die (mysqli_error($connect));
		if (mysqli_num_rows($result)==1){
			$row=mysqli_fetch_assoc($result);
			echo "<div id='search_st_result_restable' class='$row[id]'>".iconv('cp1251','utf-8',$row['first_name'])." ".iconv('cp1251','utf-8',$row['last_name'])." ".iconv('cp1251','utf-8',$row['for_father']).". $row[start_year] года поступления</div>";
		}
		else{
			echo "<div id='search_st_result_restable' class='0'>Поиск не дал результатов. Проверьте данные.</div>";
		} 
	
	
	
?>