<?php
	require_once '../../Config/db_config.php';
	session_start();
	echo '<script src="../../../Public/Client/assets/js/messageBox.js"></script>';
	$TenDangNhap = $_POST['username'];
	$Email = $_POST['email'];
	$MatKhauMoi = $_POST['password1'];
	$XacNhanMatKhau = $_POST['password2'];
	if(trim($TenDangNhap) == "")
		echo "<script> showNotify('Tên tài khoản không được bỏ trống!'); </script>";
	elseif(trim($MatKhauMoi) == "")
		echo "<script> showNotify('Mật khẩu mới không được bỏ trống!'); </script>";
	elseif(trim($Email) == "")
		echo "<script> showNotify('Email không được bỏ trống!'); </script>";
	elseif($MatKhauMoi != $XacNhanMatKhau)
		echo "<script> showNotify('Xác nhận mật khẩu không đúng!'); </script>";
	else	
	{
		$MatKhauCu= md5($MatKhauCu);
		$MatKhauMoi= md5($MatKhauMoi);
        // Kiểm tra người dùng có tồn tại không
		$sql_kiemtra = "SELECT * from tbl_taikhoankhachhang WHERE TenDangNhap = '$TenDangNhap'";			
		$danhsach = $connect->query($sql_kiemtra);
		
		//Nếu kết quả kết nối không được thì xuất báo lỗi và thoát
		if (!$danhsach) {
			die("Không thể thực hiện câu lệnh SQL: " . $connect->error);
			exit();
		}    

		if($danhsach)
		{
			$sql= "update `tbl_taikhoankhachhang`
					SET `MatKhau` = '$MatKhauMoi'
					Where `TenDangNhap` = '$TenDangNhap'";
			$doimatkhau = $connect->query($sql);
			//Nếu kết quả kết nối không được thì xuất báo lỗi và thoát
			if (!$doimatkhau) 
			{
				die("Không thể thực hiện câu lệnh SQL: " . $connect->error);
				exit();
			}
			else
			{
				echo "<script> showNotify('Chỉnh sửa thành công!'); </script>";
			}
			
		}
		else
		{
			echo "<script> showNotify('Tên tài khoản không đúng!'); </script>";
		}
			
	}	
?>