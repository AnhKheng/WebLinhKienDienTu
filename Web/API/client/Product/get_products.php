<?php
include_once '../../Config/db_config.php';

// ĐÃ SỬA LỖI TYPO TẠI ĐÂY
header('Content-Type: application/json; charset=utf-8');

if (!isset($connect) || $connect->connect_error) {
    echo json_encode(['error' => 'Lỗi hệ thống']);
    exit;
}

$category = $_GET['category'] ?? '';
$search = $_GET['search'] ?? '';
$page = max(1, (int)($_GET['page'] ?? 1));
$featured = isset($_GET['featured']) && $_GET['featured'] == '1';

// --- THAY ĐỔI LOGIC PHÂN TRANG ---

if ($featured && in_array($category, ['DM01', 'DM05', 'DM12', 'DM03']) && empty($search)) {
    // 1. Nếu là FEATURED (Nổi bật)
    $limit_per_page = 4; // 4 sản phẩm/trang
} else {
    // 2. Nếu là SEARCH (Tìm kiếm, Tất cả, Chuột, v.v.)
    $limit_per_page = 10; // Đặt 10 sản phẩm/trang (Bạn có thể đổi số này)
}

$offset = ($page - 1) * $limit_per_page;

// --- HẾT THAY ĐỔI LOGIC PHÂN TRANG ---


// === ĐẾM TỔNG SẢN PHẨM ===
$count_sql = "SELECT COUNT(*) as total FROM tbl_sanpham WHERE 1=1";
$count_params = [];
$count_types = '';

if (!empty($category)) {
    $count_sql .= " AND MaDM = ?";
    $count_params[] = $category;
    $count_types .= 's';
}
if (!empty($search)) {
    $count_sql .= " AND TenSP LIKE ?";
    $count_params[] = '%' . $search . '%';
    $count_types .= 's';
}

$count_stmt = $connect->prepare($count_sql);
if ($count_stmt && !empty($count_types)) {
    $count_stmt->bind_param($count_types, ...$count_params);
}
if ($count_stmt) $count_stmt->execute();
$count_result = $count_stmt ? $count_stmt->get_result() : null;
$total_products = $count_result ? $count_result->fetch_assoc()['total'] : 0;

// Tính tổng số trang (Áp dụng cho cả 2 trường hợp)
$total_pages = ceil($total_products / $limit_per_page);
$total_pages = max(1, $total_pages);


// === LẤY SẢN PHẨM ===
$sql = "SELECT MaSP, TenSP, DonGia, HinhAnh FROM tbl_sanpham WHERE 1=1";
$params = [];
$types = '';

if (!empty($category)) {
    $sql .= " AND MaDM = ?";
    $params[] = $category;
    $types .= 's';
}
if (!empty($search)) {
    $sql .= " AND TenSP LIKE ?";
    $params[] = '%' . $search . '%';
    $types .= 's';
}

$sql .= " LIMIT ? OFFSET ?";
$params[] = $limit_per_page; // Dùng $limit_per_page thay vì $limit
$params[] = $offset;
$types .= 'ii';

$stmt = $connect->prepare($sql);
if ($stmt === false) {
    echo json_encode(['error' => 'Lỗi truy vấn']);
    exit;
}

$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

// === TẠO MẢNG DỮ LIỆU ===
$products = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

// === TRẢ VỀ JSON ===
echo json_encode([
    'products' => $products,
    'currentPage' => $page,
    'totalPages' => $total_pages,
    'totalProducts' => $total_products
]);

$stmt->close();
if (isset($count_stmt)) $count_stmt->close();
$connect->close();
?>