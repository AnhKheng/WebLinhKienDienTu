<?php
	// Hủy SESSION
	unset($_SESSION['MaKH']);
	unset($_SESSION['TenDangNhap']);
	
	// Chuyển hướng về trang index.php
	echo "<script>window.location.href = 'Index.php';</script>";

?>