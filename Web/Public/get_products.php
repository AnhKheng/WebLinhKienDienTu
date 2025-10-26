<?php
header('Content-Type: application/json');

// Gọi file cấu hình
include_once '../API/Config/db_config.php';

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
    $params[] = "%" . strtolower($searchTerm) . "%"; // Chuyển searchTerm thành lowercase để khớp
    $types .= "s";
}

// Debug: In câu SQL và tham số để kiểm tra
// error_log("SQL: $sql, Params: " . json_encode($params));

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
} else {
    // Debug: In số dòng trả về
    // error_log("No rows returned. Num rows: " . $result->num_rows);
}

echo json_encode($products);

$stmt->close();
$connect->close();
?>