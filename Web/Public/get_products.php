<?php
header('Content-Type: application/json');

// Gọi file cấu hình
include_once '../API/Config/db_config.php';

$categoryId = isset($_GET['category']) ? $_GET['category'] : '';

$sql = "SELECT MaSP, TenSP, DonGia, HinhAnh FROM tbl_sanpham WHERE MaDM = ? OR ? = ''";
$stmt = $connect->prepare($sql);
$stmt->bind_param("ss", $categoryId, $categoryId);
$stmt->execute();
$result = $stmt->get_result();

$products = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

echo json_encode($products);

$stmt->close();
$connect->close();
?>