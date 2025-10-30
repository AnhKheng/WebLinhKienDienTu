<?php
include_once '../../Config/db_config.php';

if (!isset($connect) || $connect->connect_error) {
    http_response_code(500);
    echo "<p style='color:red;'>Lỗi hệ thống.</p>";
    exit;
}

$category = isset($_GET['category']) ? trim($_GET['category']) : '';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 4;
$offset = ($page - 1) * $limit;

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
    echo "<p style='color:red;'>Lỗi truy vấn.</p>";
    exit;
}

$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

// BẮT ĐẦU IN RA 4 CARD TRONG 1 HÀNG
echo '<div class="product-slider">';  // ← QUAN TRỌNG: MỞ SLIDER Ở ĐÂY

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $hinh = !empty($row['HinhAnh']) 
            ? '../img/' . htmlspecialchars($row['HinhAnh']) 
            : '../img//default_product.png';

        echo '
        <div class="product-card">
            <img src="' . $hinh . '" 
                 alt="' . htmlspecialchars($row['TenSP']) . '" 
                 loading="lazy"
                 onerror="this.src=\'../../../Public/img/default_product.png\'">
            <h3>' . htmlspecialchars($row['TenSP']) . '</h3>
            <p class="price">' . number_format($row['DonGia'], 0, ',', '.') . '₫</p>
            <button class="btn-buy">Mua ngay</button>
        </div>';
    }
} else {
    echo '<p style="grid-column: 1 / -1; text-align:center; color:#999;">Không có sản phẩm.</p>';
}

echo '</div>'; // ← ĐÓNG SLIDER Ở ĐÂY

$stmt->close();
$connect->close();
?>