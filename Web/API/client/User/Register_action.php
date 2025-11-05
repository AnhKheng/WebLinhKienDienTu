<?php
	require_once '../../Config/db_config.php';
	session_start();
	echo '<script src="../../../Public/Client/assets/js/messageBox.js"></script>';
	// Lấy thông tin từ FORM
	$HoVaTen = $_POST['name'];
	$TenDangNhap = $_POST['username'];
	$MatKhau = $_POST['password1'];
	$XacNhanMatKhau = $_POST['password2'];
	$Email = $_POST['email'];
	
	// Kiểm tra
	if(trim($HoVaTen) == "")
		echo "<script> showNotify('Họ và tên không được bỏ trống!'); </script>";
	elseif(trim($TenDangNhap) == "")
		echo "<script> showNotify('Tên đăng nhập không được bỏ trống!'); </script>";
	elseif(trim($MatKhau) == "")
		echo "<script> showNotify('Mật khẩu không được bỏ trống!'); </script>";
	elseif($MatKhau != $XacNhanMatKhau)
		echo "<script> showNotify('Xác nhận mật khẩu không đúng!'); </script>";
	elseif(trim($Email) == "")
		echo "<script> showNotify('Email không được bỏ trống!'); </script>";
	else
	{	
		// Kiểm tra người dùng đã tồn tại chưa
		$sql_kiemtra = "SELECT * FROM tbl_taikhoankhachhang WHERE TenDangNhap = '$TenDangNhap'";
		
		$danhsach = $connect->query($sql_kiemtra);
		
		if ($danhsach && $danhsach->num_rows == 0) 
		{

			// Tự động tạo MaKH mới (dạng KH01, KH02, ...)
			$sql_max = "SELECT MaKH FROM tbl_khachhang ORDER BY MaKH DESC LIMIT 1";
			$result_max = $connect->query($sql_max);	
			
			if ($result_max && $row = $result_max->fetch_assoc()) 
			{
				$lastMa = $row['MaKH'];
				$number = intval(substr($lastMa, 2)) + 1;
				$MaKH = "KH" . str_pad($number, 2, "0", STR_PAD_LEFT);
			} 
			else 
			{
				$MaKH = "KH01";
			}

			
			$sql_themkh = "INSERT INTO `tbl_khachhang`(`MaKH`, `TenKH`)
					VALUES ('$MaKH', '$HoVaTen')";
			$themkhachhang = $connect->query($sql_themkh);
			
			if($themkhachhang)
			{
				echo "<script> showNotify('Thêm khách hàng thành công!'); </script>";
			}
			else
			{
				echo "<script> showNotify('" .$connect->error. "'); </script>";
			}
            
            // Mã hóa mật khẩu
			$MatKhau = md5($MatKhau);
            date_default_timezone_set('Asia/Ho_Chi_Minh');
            $today = date('Y-m-d H:i:s');

            $sql_themtkkh = "INSERT INTO `tbl_taikhoankhachhang`(`MaKH`, `LoaiDangNhap`, `TenDangNhap`, `Email`, `MatKhau`, `NgayTao`, `TrangThai`)
                    VALUES ('$MaKH', 'local', '$TenDangNhap', '$Email', '$MatKhau', '$today', 1)";
            $themtkkh = $connect->query($sql_themtkkh);

            if($themtkkh)
			{
				echo "<script> showNotify('Thêm tài khoản khách hàng thành công!'); </script>";
			}
			else
			{
				echo "<script> showNotify('" .$connect->error. "'); </script>";
			}
		}
		else
		{
			echo "<script> showNotify('Người dùng với tên đăng nhập đã được sử dụng!'); </script>";
		}
	}
?>