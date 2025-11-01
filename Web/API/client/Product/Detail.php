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

// Tạo đối tượng sản phẩm để trả về
$product_data = [
    'MaSP' => $row['MaSP'],
    'TenSP' => htmlspecialchars($row['TenSP']),
    'DonGia' => $row['DonGia'],
    'HinhAnh' => $row['HinhAnh'],
    'MoTa' => $mota ?: '<em>Chưa có mô tả cho sản phẩm này.</em>'
];

echo json_encode(['product' => $product_data, 'id' => $id]);

$stmt->close();
$connect->close();
?>