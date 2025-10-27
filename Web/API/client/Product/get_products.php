<?php
header('Content-Type: application/json');

// Gọi file cấu hình với đường dẫn tuyệt đối
include_once 'D:/VertrigoServ/www/WebLinhKienDienTu/Web/API/Config/db_config.php';

$categoryId = isset($_GET['category']) ? $_GET['category'] : '';
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

$sql = "SELECT MaSP, TenSP, DonGia, HinhAnh FROM tbl_sanpham WHERE 1=1";
$params = [];
$types = "";

if (!empty($categoryId)) {
    $sql .= " AND MaDM = ?";
    $params[] = $categoryId;
    $types .= "s";
}

if (!empty($searchTerm)) {
    $sql .= " AND LOWER(TenSP) LIKE ?";
    $params[] = "%" . $searchTerm . "%";
    $types .= "s";
}

$stmt = $connect->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
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