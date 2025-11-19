<?php
include_once '../../API/Config/db_config.php';
include_once '../../API/client/Cart/Cart_api.php';

$gioHangBUS = new GioHangBUS($connect);
$maTKKH = $_SESSION['MaTKKH'] ?? null;
$action = $_GET['action'] ?? '';
$maSP = $_GET['MaSP'] ?? '';
$input = json_decode(file_get_contents('php://input'), true);

if (!$maTKKH) {
    header("Location: Index.php");
    exit;
}

$tongtien = 0;

date_default_timezone_set('Asia/Ho_Chi_Minh');
$today = date('Y-m-d H:i:s');

$maGH = $gioHangBUS->layGioHang($maTKKH);

if ($action == 'mua' && $maTKKH !=null) 
{
    $gioHangBUS->themSanPham($maTKKH, $maSP);
}

if ($action == 'them' && $maTKKH !=null) 
{
    $gioHangBUS->themSanPham($maTKKH, $maSP);
    header("Location: Index.php?do=Details&id={$maSP}");
    exit();
}

if ($action == 'xoa' && $maSP)
{
    if ($gioHangBUS->xoaSanPham($maTKKH, $maSP)) 
    {
        echo "<script>alert('Đã xóa sản phẩm khỏi giỏ hàng!'); window.location.href ='Index.php?do=CartForm';</script>";
    } 
    else 
    {
        echo "<script>alert('Xóa thất bại!'); window.history.back();</script>";
    }
}

if ($action == 'tang' && $maSP) {
    if ($gioHangBUS->tangSanPham($maTKKH, $maSP)) {
        header("Location: Index.php?do=CartForm");
        exit();
    }
}

if ($action == 'giam' && $maSP) {
    if ($gioHangBUS->giamSanPham($maTKKH, $maSP)) {
        header("Location: Index.php?do=CartForm");
        exit();
    }
}

?>
<link rel="stylesheet" href="assets/css/cart.css">

<table style="width:100%; border-collapse:collapse; text-align:center;">
    <tr>
        <th width=130px>Hình ảnh</th>
        <th width=300px>Tên sản phẩm</th>
        <th width=130px>Giá bán</th>
        <th width=130px>Số lượng</th>
        <th width=130px>Thành tiền</th>
        <th width=130px>Thao tác</th>
    </tr>

    <?php
        if ($maGH) {
            $chiTiet = $gioHangBUS->layChiTiet($maGH);
            $themsp = $gioHangBUS->themSanPham($maTKKH, $maSP);
            $giamsp = $gioHangBUS->giamSanPham($maTKKH, $maSP);
            while ($chiTiet && $row = $chiTiet->fetch_array(MYSQLI_ASSOC)) {
                $thanhtien = $row['DonGia'] * $row['SoLuong'];
                $tongtien += $thanhtien;

                $hinhAnh = $row['HinhAnh'];
                // Nếu chỉ lưu tên file trong DB, ví dụ "chuot.jpg"
                if (strpos($hinhAnh, '/') === false) {
                    $hinhAnh = "../../Public/img/" . $hinhAnh;
                }

                echo "<tr>";
                echo "<td><img src='$hinhAnh' width='130' height='130'></td>";
                echo "<td>{$row['TenSP']}</td>";
                echo "<td>" . number_format($row['DonGia']) . " $</td>";
                echo "<td>
                        <a href='Index.php?do=CartForm&action=giam&MaSP={$row['MaSP']}'> - </a>
                        {$row['SoLuong']}
                        <a href='Index.php?do=CartForm&action=tang&MaSP={$row['MaSP']}'> + </a>
                    </td>";
                echo "<td>" . number_format($thanhtien) . " $</td>";
                echo "<td><a href='Index.php?do=CartForm&action=xoa&MaSP={$row['MaSP']}'>Xóa</a></td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='6' style='text-align:center;'>Không có sản phẩm trong giỏ hàng</td></tr>";
        }
    ?>

    <tr>
        <td colspan="4" style="text-align:right; vertical-align: middle; padding-right: 10px;">
            <strong>Tổng cộng:</strong>
        </td>
        <td style="vertical-align: middle;">
            <strong><?= number_format($tongtien) ?> $</strong>
        </td>
        <td style="padding: 10px;"> 
            <input type="text" id="diaChiNhanHang" placeholder="Nhập địa chỉ nhận hàng..." 
                   style="width: 90%; padding: 8px; margin-bottom: 5px; border: 1px solid #ccc; border-radius: 4px;">
            
            <button onclick="kiemTraVaDatHang()" 
                    style="background-color:#007bff; color:white; padding:8px 20px; border:none; border-radius:5px; cursor:pointer; width: 90%;">
                Đặt hàng
            </button>
        </td>
    </tr>
</table>

<script>
    function kiemTraVaDatHang() {
        var diaChi = document.getElementById('diaChiNhanHang').value.trim();
        
        if (diaChi === "") {
            alert("Vui lòng nhập địa chỉ nhận hàng trước khi đặt hàng!");
            document.getElementById('diaChiNhanHang').focus();
            return;
        }
        
        // Chuyển hướng sang trang Checkout kèm theo địa chỉ trên URL
        window.location.href = "Index.php?do=CheckoutForm&address=" + encodeURIComponent(diaChi);
    }
</script>