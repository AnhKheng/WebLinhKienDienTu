<?php
	// Lấy thông tin từ FORM
	$TenDangNhap = $_POST['Username'];
	$MatKhau = $_POST['Password'];
	
	// Kiểm tra
	if(trim($TenDangNhap) == "")
		ThongBaoLoi("Tên đăng nhập không được bỏ trống!");
	elseif(trim($MatKhau) == "")
		ThongBaoLoi("Mật khẩu không được bỏ trống!");
	else
	{
		// Mã hóa mật khẩu
		//$MatKhau = md5($MatKhau);
		
		// Kiểm tra người dùng có tồn tại không
		$sql_kiemtra = "SELECT * from tbl_taikhoankhachhang WHERE TenDangNhap = '$TenDangNhap' AND MatKhau = '$MatKhau'";	
		
		
		$danhsach = $connect->query($sql_kiemtra);
		
		//Nếu kết quả kết nối không được thì xuất báo lỗi và thoát
		if (!$danhsach) {
			die("Không thể thực hiện câu lệnh SQL: " . $connect->error);
			exit();
		}
		
		$dong = $danhsach->fetch_array(MYSQLI_ASSOC);
		if($dong)
		{
			// Đăng ký SESSION
			$_SESSION['MaKH'] = $dong['MaKH'];
			$_SESSION['MaTKKH'] = $dong['MaTKKH'];
			$_SESSION['TenDangNhap'] = $dong['TenDangNhap'];

			
			// Chuyển hướng về trang index.php
			echo "<script>window.location.href = 'Index.php';</script>";

		
		}
		else
		{
			echo "<p>Tài khoản hoặc mật khẩu không đúng.</p>";
		}
	}
	
?>