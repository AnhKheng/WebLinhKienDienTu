<?php
	header("Content-type: text/html; charset=utf-8");
	$severname ="localhost";
	$username= "root";
	$password = "vertrigo";
	$dbname = "shop_linhkien";
	$connect = new mysqli($severname,$username,$password,$dbname);
	
	mysqli_set_charset($connect,'UTF8');
	if($connect->connect_error){
		die("Khong ket noi: " . $conn->connect_error);
		exit();
	}
?>
