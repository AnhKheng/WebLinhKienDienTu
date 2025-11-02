<?php
include_once '../../Config/db_config.php';

header('Content-Type: application/json; charset=utf-8');

if (!isset($connect) || $connect->connect_error) {
    echo json_encode(['error' => 'Lỗi hệ thống']);
    exit;
}

$id = $_GET['id'] ?? '';
if (empty($id)) {
    echo json_encode(['error' => 'Không có mã sản phẩm']);
    exit;
}

// === 1. LẤY SẢN PHẨM CHÍNH ===
$sql = "SELECT MaSP, TenSP, DonGia, HinhAnh, MoTa FROM tbl_sanpham WHERE MaSP = ? LIMIT 1";
$stmt = $connect->prepare($sql);
$mota = '';

if ($stmt === false) {
    // Fallback: try without MoTa if column doesn't exist
    $sql2 = "SELECT MaSP, TenSP, DonGia, HinhAnh FROM tbl_sanpham WHERE MaSP = ? LIMIT 1";
    $stmt = $connect->prepare($sql2);
    if ($stmt === false) {
        echo json_encode(['error' => 'Lỗi truy vấn']);
        exit;
    }
    $stmt->bind_param('s', $id);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res ? $res->fetch_assoc() : null;
    $mota = '';
} else {
    $stmt->bind_param('s', $id);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res ? $res->fetch_assoc() : null;
    $mota = isset($row['MoTa']) ? $row['MoTa'] : '';
}

if (!$row) {
    echo json_encode(['error' => 'Không tìm thấy sản phẩm']);
    exit;
}

// Tạo đối tượng sản phẩm chính
$product_data = [
    'MaSP' => $row['MaSP'],
    'TenSP' => htmlspecialchars($row['TenSP']),
    'DonGia' => $row['DonGia'],
    'HinhAnh' => $row['HinhAnh'],
    'MoTa' => $mota ?: '<em>Chưa có mô tả cho sản phẩm này.</em>'
];
$stmt->close();


// === 2. LẤY SẢN PHẨM DỊCH VỤ (SP22 VÀ SP34) ===
$services = [];
// SỬA CÂU SQL: Thêm HinhAnh
$sql_services = "SELECT MaSP, TenSP, DonGia, HinhAnh FROM tbl_sanpham WHERE MaSP = 'SP22' OR MaSP = 'SP34'";
$res_services = $connect->query($sql_services);

if ($res_services && $res_services->num_rows > 0) {
    while ($service_row = $res_services->fetch_assoc()) {
        $services[] = $service_row;
    }
}

// === 3. TRẢ VỀ JSON (BAO GỒM CẢ DỊCH VỤ) ===
echo json_encode([
    'product' => $product_data, 
    'services' => $services, // Thêm mảng services vào
    'id' => $id
]);

$connect->close();
?>