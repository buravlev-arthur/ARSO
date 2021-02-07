<?php
error_reporting(E_ERROR);
 define(DB_SERVER,"localhost");
 define(DB_USER,"u1156424_default");
 define(DB_PASSWORD,"o!e!3DNm");
 define(DB_NAME,"u1156424_arso");
 $connect = mysqli_connect(DB_SERVER,DB_USER,DB_PASSWORD) or die ("невозможно установить соединение с базой данных");
 mysqli_select_db($connect,DB_NAME) or die ("нет доступка к базе данных ARSO");
	$sql = "
	CREATE TABLE groups_structure (
		id varchar(26) NOT NULL,
		special varchar(45) NOT NULL,
		course int NOT NULL,
		PRIMARY KEY (id)
	)";
	mysqli_query($connect,$sql) or die (mysqli_error($connect));
	$sql = "
	CREATE TABLE specials (
		id varchar(45) NOT NULL,
		name varchar(65),
		PRIMARY KEY (id)
	)";
	mysqli_query($connect,$sql) or die (mysqli_error($connect));
	$sql = "
	CREATE TABLE students (
		id int NOT NULL auto_increment,
		first_name varchar(60),
		last_name varchar(60),
		for_father varchar(80),
		burn_date varchar(30),
		male varchar(12),
		special varchar(45),
		start_year int,
		vocation int,
		login varchar(255),
		password varchar(255),
		status varchar(26),
		PRIMARY KEY (id)
	)";
	mysqli_query($connect,$sql) or die (mysqli_error($connect));
	$sql = "
	CREATE TABLE teachers (
		id int NOT NULL auto_increment,
		first_name varchar(60),
		last_name varchar(60),
		for_father varchar(80),
		burn_date varchar(30),
		male varchar(12),
		start_year int,
		login varchar(255),
		password varchar(255),
		status varchar(26),
		PRIMARY KEY (id)
	)";
	mysqli_query($connect,$sql) or die (mysqli_error($connect));
	$sql="
	CREATE TABLE objects (
		id int NOT NULL auto_increment,
		name varchar(120),
		teacher int,
		the_group varchar(65),
		year varchar(30),
		part_of_year int,
		PRIMARY KEY (id)
	)";
	mysqli_query($connect,$sql) or die (mysqli_error($connect));
	mysqli_close($connect);
?>