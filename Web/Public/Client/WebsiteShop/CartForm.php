<?php
    session_start();
    $tongtien = 0;
    date_default_timezone_set('Asia/Ho_Chi_Minh');
    $today = date('Y-m-d H:i:s');
    $maTKKH = $_SESSION['MaTKKH'];
    $MaSP = $_POST['MaSP'];
?>
<table>
    <tr>
        <th width=130px> Hình ảnh </th>
        <th width=300px> Tên sản phẩm </th>
        <th width=130px> Giá bán </th>
        <th width=130px> Số lượng </th>
        <th width=130px> Thành tiền </th>
        <th width=130px> Thao tác </th>
    </tr>
    
    <tr>
        <?php
            $sql_cart = "SELECT * from tbl_giohang WHERE MaTKKH = '$maTKKH' AND TrangThai = 'active'";
            $cart = $connect->query($sql_cart);

            //Tạo giỏ hàng mới nếu chưa có
            if ($cart && $cart->num_rows == 0) 
            {
                $sql_themgh = "INSERT INTO `tbl_giohang`(`MaTKKH`)
					VALUES ('$maTKKH')"; 
            }



            $sql_cart_view = "SELECT MaGH from tbl_giohang WHERE MaTKKH = '$maTKKH' AND TrangThai = 'active'";
            $cart_view = $connect->query($sql_cart_view);
            if ($cart_view && $row = $cart_view->fetch_assoc()) 
			{
                $maGH = $row['MaGH'];
            }
            
            $sql_cart_view_ct = "SELECT * from tbl_chitietgiohang WHERE MaGH ='$maGH'";
            $cart_view_ct = $connect->query($sql_cart_view_ct);
            foreach ($cart_view_ct as $MaSP => $item)
		    {
                
            }

			foreach ($cart as $MaSP => $item) 
            {
				$thanhtien = $item['GiaBan'] * $item['soluong'];
				$tongtien += $thanhtien;
				echo "<tr>";
				echo "<td><img src='{$item['HinhAnh']}' width='130' height='130'></td>";
				echo "<td>{$item['TenSP']}</td>";
				echo "<td>" . number_format($item['GiaBan']) . " $</td>";
				echo "<td>
						<a href='index.php?do=giohang1&action=giam&MaSP=" .$MaSP. "'>-</a>
						{$item['soluong']}
					    <a href='index.php?do=giohang1&action=tang&MaSP=" .$MaSP. "'>+</a>
					</td>";
				echo "<td>" . number_format($thanhtien) . " $</td>";
				echo "<td><a href='index.php?do=giohang1&action=xoa&MaSP=" .$MaSP. "'>Xóa</a></td>";
				echo "</tr>";
															
			}
				echo "<tr>";
				echo "<td colspan='4' style='text-align:right;'>Tổng cộng:</td>";
				echo "<td><span class=\"giaban\">" . number_format($tongtien) . " $</span></td>";
				echo "<td></td>";
				echo "</tr>";
								
				$sale=$_SESSION['CapTK']*10;
				echo "<tr>";
				echo "<td colspan='4' style='text-align:right;'>Sale tài khoản:</td>";
				echo "<td><span class=\"giaban\">" . number_format($sale) . "%</span></td>";
				echo "<td></td>";
				echo "</tr>";
								
				$thanhtientong=$tongtien*(100-$sale)/100;
				echo "<tr>";
				echo "<td colspan='4' style='text-align:right;'>Thành tiền:</td>";
				echo "<td><span class=\"giaban\">" . number_format($thanhtientong) . " $</span></td>";
				echo "<td></td>";
				echo "</tr>";

				echo "</table>";
				echo "<form method='post'>";
				echo "</br> <div class=\"thanhtoan\"><input type='submit' name='thanhtoan' value='Thanh toán' /> </div>";
			    echo "</form>";
        ?>
    </tr>
    <tr>
        <td colspan="4" style="text-align:right;"> Tổng cộng: </td>
        <td><span class="tongcong">  </span></td>
    </tr>
</table>