<?php
include_once '../../API/Config/db_config.php';
include_once '../../API/client/Checkout/Checkout_api.php';

// Khởi tạo lớp BUS
$muaHangBUS = new MuaHangBUS($connect);

// Lấy mã tài khoản khách hàng từ session
$maTKKH = $_SESSION['MaTKKH'] ?? null;

// Lấy địa chỉ từ URL (được truyền từ CartForm)
$diaChi = $_GET['address'] ?? '';

if (!$maTKKH) {
    header("Location: Index.php");
    exit;
}

// Nếu người dùng đã chọn cửa hàng và nhấn xác nhận
if (isset($_POST['btnXacNhan'])) {
    $maCH = $_POST['MaCH'] ?? '';
    $diaChiPost = $_POST['DiaChi'] ?? ''; // Lấy địa chỉ từ form

    if (empty($maCH)) {
        echo "<script>alert('Vui lòng chọn cửa hàng!'); window.history.back();</script>";
        exit;
    }

    // Gọi xử lý mua hàng từ BUS (Truyền thêm địa chỉ)
    $ketQua = $muaHangBUS->xuLyMuaHang($maTKKH, $maCH, $diaChiPost);

    // --- THAY ĐỔI LOGIC XỬ LÝ KẾT QUẢ ---
    if (is_array($ketQua) && $ketQua['success'] == true) {
        // Thành công! Lấy MaHD
        $maHD = $ketQua['MaHD'];
        // Chuyển hướng đến trang tóm tắt đơn hàng (Yêu cầu 1)
        echo "<script>
                alert('Đặt hàng thành công! Mã hóa đơn của bạn là: $maHD');
                window.location.href='Index.php?do=OrderSummary&MaHD=$maHD';
              </script>";
    } else {
        // Thất bại, hiển thị lỗi
        $errorMessage = is_array($ketQua) ? $ketQua['message'] : 'Đã xảy ra lỗi không xác định.';
        echo "<script>alert('Lỗi đặt hàng: $errorMessage'); window.history.back();</script>";
    }
    exit;
    // --- KẾT THÚC THAY ĐỔI ---
}
?>

<link rel="stylesheet" href="assets/css/cart.css">

<div style="width: 600px; margin: 50px auto; text-align: center; border: 1px solid #ccc; padding: 20px; border-radius: 10px; background-color: #fff;">
    <h2 style="color: #28a745;">📦 Xác nhận đặt hàng</h2>
    <p>Vui lòng kiểm tra thông tin trước khi xác nhận:</p>

    <form method="POST" action="">
        
        <div style="text-align: left; margin-bottom: 15px; padding: 0 10%;">
            <label style="font-weight: bold;">Địa chỉ nhận hàng:</label>
            <input type="text" name="DiaChi" value="<?php echo htmlspecialchars($diaChi); ?>" readonly 
                   style="width: 100%; padding: 10px; background: #f9f9f9; border: 1px solid #ddd; border-radius: 4px; margin-top: 5px; color: #555;">
        </div>

        <div style="text-align: left; margin-bottom: 20px; padding: 0 10%;">
            <label style="font-weight: bold;">Chọn cửa hàng xử lý:</label>
            <select name="MaCH" id="MaCH" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; margin-top: 5px;">
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
        </div>

        <br>
        <input type="submit" name="btnXacNhan" value="Xác nhận đặt hàng"
               style="background-color:#28a745; color:white; padding:12px 30px; border:none; border-radius:5px; cursor:pointer; font-size: 16px; font-weight: bold;">
    </form>

    <br>
    <a href="Index.php?do=CartForm" style="color:#007bff; text-decoration:none;">← Quay lại giỏ hàng</a>
</div>