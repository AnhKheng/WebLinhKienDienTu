<?php
include_once '../../API/Config/db_config.php';
include_once '../../API/client/Checkout/Checkout_api.php';

// Khởi tạo lớp BUS
$muaHangBUS = new MuaHangBUS($connect);

// Lấy mã tài khoản khách hàng từ session
$maTKKH = $_SESSION['MaTKKH'] ?? null;

if (!$maTKKH) {
    header("Location: Index.php");
    exit;
}

// Nếu người dùng đã chọn cửa hàng và nhấn xác nhận
if (isset($_POST['btnXacNhan'])) {
    $maCH = $_POST['MaCH'] ?? '';

    if (empty($maCH)) {
        echo "<script>alert('Vui lòng chọn cửa hàng!'); window.history.back();</script>";
        exit;
    }

    // Gọi xử lý mua hàng từ BUS
    $ketQua = $muaHangBUS->xuLyMuaHang($maTKKH, $maCH);

    // --- THAY ĐỔI LOGIC XỬ LÝ KẾT QUẢ ---
    if (is_array($ketQua) && $ketQua['success'] == true) {
        // Thành công! Lấy MaHD
        $maHD = $ketQua['MaHD'];
        // Chuyển hướng đến trang tóm tắt đơn hàng (Yêu cầu 1)
        echo "<script>
                alert('Thanh toán thành công! Mã hóa đơn của bạn là: $maHD');
                window.location.href='Index.php?do=OrderSummary&MaHD=$maHD';
              </script>";
    } else {
        // Thất bại, hiển thị lỗi
        $errorMessage = is_array($ketQua) ? $ketQua['message'] : 'Đã xảy ra lỗi không xác định.';
        echo "<script>alert('Lỗi thanh toán: $errorMessage'); window.history.back();</script>";
    }
    exit;
    // --- KẾT THÚC THAY ĐỔI ---
}
?>

<link rel="stylesheet" href="assets/css/cart.css">

<div style="width: 600px; margin: 50px auto; text-align: center; border: 1px solid #ccc; padding: 20px; border-radius: 10px;">
    <h2>🛒 Chọn cửa hàng thanh toán</h2>
    <p>Vui lòng chọn cửa hàng nơi bạn muốn nhận hàng:</p>

    <form method="POST" action="">
        <select name="MaCH" id="MaCH" required style="padding: 8px; width: 80%; margin-bottom: 20px;">
            <option value="">-- Chọn cửa hàng --</option>
            <?php
            // Lấy danh sách cửa hàng từ DB
            $sql = "SELECT MaCH, TenCH, DiaChi FROM tbl_cuahang";
            $result = $connect->query($sql);

            while ($result && $row = $result->fetch_assoc()) {
                echo "<option value='{$row['MaCH']}'>{$row['TenCH']} - {$row['DiaChi']}</option>";
            }
            ?>
        </select>
        <br>
        <input type="submit" name="btnXacNhan" value="Xác nhận thanh toán"
               style="background-color:#28a745; color:white; padding:10px 25px; border:none; border-radius:5px; cursor:pointer;">
    </form>

    <br>
    <a href="Index.php?do=CartForm" style="color:#007bff; text-decoration:none;">← Quay lại giỏ hàng</a>
</div>