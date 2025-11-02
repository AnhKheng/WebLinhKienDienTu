<?php
include_once '../../Config/db_config.php';

header('Content-Type: application/json; charset=utf-8');

if (!isset($connect) || $connect->connect_error) {
    echo json_encode(['error' => 'Lỗi hệ thống']);
    exit;
}

$category = $_GET['category'] ?? '';
$search = $_GET['search'] ?? '';
$page = max(1, (int)($_GET['page'] ?? 1));
$featured = isset($_GET['featured']) && $_GET['featured'] == '1';
$limit_per_page = 4; // mỗi trang 4 sản phẩm

$isFeatured = $featured 
              && in_array($category, ['DM01', 'DM05', 'DM12', 'DM03']) 
              && empty($search);

$limit = $isFeatured ? $limit_per_page : 100;
$offset = ($page - 1) * $limit;

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

// Tính tổng số trang
if ($isFeatured) {
    $total_pages = ceil($total_products / $limit_per_page);
    $total_pages = max(1, $total_pages);
} else {
    $total_pages = 1;
}

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
$params[] = $limit;
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

// === TẠO HTML ===
ob_start();
if ($isFeatured) {
    echo '<div class="product-slider">';
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $hinh = !empty($row['HinhAnh']) 
                ? '../img/' . htmlspecialchars($row['HinhAnh']) 
                : '../img/default_product.png';

            echo '
            <div class="product-card">
                <img src="' . $hinh . '" 
                     alt="' . htmlspecialchars($row['TenSP']) . '" 
                     loading="lazy"
                     onerror="this.src=\'../img/default_product.png\'">
                <h3>' . htmlspecialchars($row['TenSP']) . '</h3>
                <p class="price">' . number_format($row['DonGia'], 0, ',', '.') . '₫</p>
                <button class="btn-buy">Mua ngay</button>
            </div>';
        }
    }
    echo '</div>';
} else {
    echo '<div class="product-grid">';
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $hinh = !empty($row['HinhAnh']) 
                ? '../img/' . htmlspecialchars($row['HinhAnh']) 
                : '../img/default_product.png';

            echo '
            <div class="product-card">
                <img src="' . $hinh . '" 
                     alt="' . htmlspecialchars($row['TenSP']) . '" 
                     loading="lazy"
                     onerror="this.src=\'../img/default_product.png\'">
                <h3>' . htmlspecialchars($row['TenSP']) . '</h3>
                <p class="price">' . number_format($row['DonGia'], 0, ',', '.') . '₫</p>
                <button class="btn-buy">Mua ngay</button>
            </div>';
        }
    } else {
        echo '<p style="grid-column: 1/-1; text-align:center; color:#999; padding:20px;">Không tìm thấy sản phẩm.</p>';
    }
    echo '</div>';
}
$html = ob_get_clean();

// === TRẢ VỀ JSON ===
echo json_encode([
    'html' => $html,
    'currentPage' => $page,
    'totalPages' => $total_pages,
    'totalProducts' => $total_products
]);

$stmt->close();
if (isset($count_stmt)) $count_stmt->close();
$connect->close();
?>