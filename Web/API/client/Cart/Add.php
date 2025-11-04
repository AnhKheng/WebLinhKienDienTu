<?php
include_once '../../Config/db_config.php';
session_start();

// Bật debug tạm thời
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json; charset=utf-8');

// Kiểm tra đăng nhập
if (!isset($_SESSION['MaKH'])) {
    echo json_encode(['success' => false, 'message' => 'Bạn cần đăng nhập để thực hiện chức năng này.']);
    exit;
}

$maTKKH = $_SESSION['MaTKKH'];

// Kiểm tra kết nối DB
if (!isset($connect) || $connect->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Lỗi hệ thống: Kết nối database không khả dụng.']);
    exit;
}

// Lấy mã sản phẩm từ GET
$maSP = $_GET['id'] ?? '';

if (empty($maSP)) {
    echo json_encode(['success' => false, 'message' => 'Không có mã sản phẩm.']);
    exit;
}

$ngay_hien_tai = date('Y-m-d H:i:s');
$SoLuong = 1;

// Kiểm tra giỏ hàng hiện tại
$sql = "SELECT MaGH FROM tbl_giohang WHERE MaTKKH = '$maTKKH' AND TrangThai = 'active' LIMIT 1";
$result = mysqli_query($connect, $sql);

if (!$result) {
    echo json_encode(['success' => false, 'message' => 'Lỗi truy vấn kiểm tra giỏ hàng: ' . mysqli_error($connect)]);
    exit;
}

if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $maGH = $row['MaGH'];
} else {
    // Tạo giỏ hàng mới
    $sql_gh = "INSERT INTO tbl_giohang (MaTKKH, NgayTao, TrangThai) VALUES ('$maTKKH', '$ngay_hien_tai', 'active')";
    if (!mysqli_query($connect, $sql_gh)) {
        echo json_encode(['success' => false, 'message' => 'Lỗi tạo giỏ hàng: ' . mysqli_error($connect)]);
        exit;
    }
    $maGH = mysqli_insert_id($connect);
}

// Kiểm tra xem sản phẩm đã có trong giỏ chưa
$sql_check = "SELECT * FROM tbl_chitietgiohang WHERE MaGH = '$maGH' AND MaSP = '$maSP'";
$result_check = mysqli_query($connect, $sql_check);

if (mysqli_num_rows($result_check) > 0) {
    // Nếu có rồi, tăng số lượng
    $sql_update = "UPDATE tbl_chitietgiohang SET SoLuong = SoLuong + 1 WHERE MaGH = '$maGH' AND MaSP = '$maSP'";
    if (!mysqli_query($connect, $sql_update)) {
        echo json_encode(['success' => false, 'message' => 'Lỗi cập nhật số lượng: ' . mysqli_error($connect)]);
        exit;
    }
    echo json_encode(['success' => true, 'message' => 'Đã cập nhật số lượng sản phẩm trong giỏ hàng.']);
} else {
    // Nếu chưa có, thêm mới
    $sql_add = "INSERT INTO tbl_chitietgiohang (MaGH, MaSP, SoLuong) VALUES ('$maGH', '$maSP', '$SoLuong')";
    if (!mysqli_query($connect, $sql_add)) {
        echo json_encode(['success' => false, 'message' => 'Lỗi thêm sản phẩm vào giỏ hàng: ' . mysqli_error($connect)]);
        exit;
    }
    echo json_encode(['success' => true, 'message' => 'Đã thêm sản phẩm vào giỏ hàng thành công.']);
}
?>
