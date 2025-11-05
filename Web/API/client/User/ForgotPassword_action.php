<?php
	$TenDangNhap = $_POST['username'];
	$Email = $_POST['email'];
	$MatKhauMoi = $_POST['password1'];
	$XacNhanMatKhau = $_POST['password2'];
	if(trim($TenDangNhap) == "")
		ThongBaoLoi("Tên tài khoản không được bỏ trống!");
	elseif(trim($MatKhauMoi) == "")
		ThongBaoLoi("Mật khẩu mới không được bỏ trống!");	
	elseif(trim($Email) == "")
		ThongBaoLoi("Email không được bỏ trống!");
	elseif($MatKhauMoi != $XacNhanMatKhau)
		ThongBaoLoi("Xác nhận mật khẩu không đúng!");
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
				ThongBao("Chỉnh sửa thành công!");
			}
			
		}
		else
		{
			ThongBao("Tên tài khoản không đúng!");
		}
			
	}	
?>