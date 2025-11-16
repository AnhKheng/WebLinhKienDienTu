<?php
// Kiểm tra đăng nhập
if (!isset($_SESSION['MaKH'])) {
    echo "<h2>Vui lòng đăng nhập để xem thông tin.</h2>";
    exit;
}

// Kiểm tra xem có MaHD không
if (!isset($_GET['MaHD'])) {
    echo "<h2>Không tìm thấy mã hóa đơn.</h2>";
    exit;
}

include_once '../../API/Config/db_config.php';

$maHD = $_GET['MaHD'];
$maKH = $_SESSION['MaKH']; // Lấy MaKH từ session (đã được lưu khi đăng nhập)
$tongTienDonHang = 0;

// Truy vấn để lấy chi tiết đơn hàng VỪA MỚI THANH TOÁN
// Chúng ta join 4 bảng:
// 1. tbl_hoadonban (hd): Để lấy trạng thái MaNV và đảm bảo đúng MaKH
// 2. tbl_chitiethoadon (cthd): Để lấy MaSP, SoLuong, DonGia
// 3. tbl_sanpham (sp): Để lấy TenSP, HinhAnh
$sql = "SELECT
            sp.HinhAnh,
            sp.TenSP,
            cthd.DonGia,
            cthd.SoLuong,
            (cthd.DonGia * cthd.SoLuong) AS ThanhTien,
            CASE
                WHEN hd.MaNV IS NULL THEN 'Đang xử lý'
                ELSE 'Đã xử lý'
            END AS TrangThaiXuLy
        FROM
            tbl_chitiethoadon AS cthd
        JOIN
            tbl_sanpham AS sp ON cthd.MaSP = sp.MaSP
        JOIN
            tbl_hoadonban AS hd ON cthd.MaHD = hd.MaHD
        WHERE
            hd.MaHD = ? AND hd.MaKH = ?
        ";

$stmt = $connect->prepare($sql);
$stmt->bind_param('ss', $maHD, $maKH);
$stmt->execute();
$result = $stmt->get_result();

?>

<link rel="stylesheet" href="assets/css/cart.css">

<h2 style="text-align:center; margin-top: 20px;">Chi tiết đơn hàng: <?php echo htmlspecialchars($maHD); ?></h2>

<table style="width:100%; border-collapse:collapse; text-align:center;">
    <tr>
        <th width=130px>Hình ảnh</th>
        <th width=300px>Tên sản phẩm</th>
        <th width=130px>Giá bán</th>
        <th width=130px>Số lượng</th>
        <th width=130px>Thành tiền</th>
        <th width=130px>Trạng thái</th>
    </tr>

    <?php
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $tongTienDonHang += $row['ThanhTien'];
            
            // Xử lý đường dẫn ảnh (giống như trong CartForm.php)
            $hinhAnh = $row['HinhAnh'];
            if (strpos($hinhAnh, '/') === false) {
                $hinhAnh = "../../Public/img/" . $hinhAnh;
            }

            echo "<tr>";
            echo "<td><img src='" . htmlspecialchars($hinhAnh) . "' width='130' height='130'></td>";
            echo "<td>" . htmlspecialchars($row['TenSP']) . "</td>";
            echo "<td>" . number_format($row['DonGia']) . " đ</td>";
            echo "<td>" . htmlspecialchars($row['SoLuong']) . "</td>";
            echo "<td>" . number_format($row['ThanhTien']) . " đ</td>";
            echo "<td><strong>" . htmlspecialchars($row['TrangThaiXuLy']) . "</strong></td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='6'>Không tìm thấy chi tiết đơn hàng này hoặc đơn hàng không thuộc về bạn.</td></tr>";
    }
    $stmt->close();
    ?>

    <tr>
        <td colspan="4" style="text-align:right; font-weight:bold;">Tổng cộng đơn hàng:</td>
        <td colspan="2" style="font-weight:bold;"><?= number_format($tongTienDonHang) ?> đ</td>
    </tr>
</table>