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
$featured = isset($_GET['featured']) && $_GET['featured'] == '1';

// XÁC ĐỊNH LÀ DANH MỤC NỔI BẬT
$isFeatured = $featured 
              && in_array($category, ['DM01', 'DM05', 'DM12', 'DM03']) 
              && empty($search);

$limit = $isFeatured ? 4 : 100; // 100 = hiển thị hết
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

// === DANH MỤC NỔI BẬT: 4 SẢN PHẨM NGANG ===
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

// === TÌM KIẾM HOẶC DANH MỤC THƯỜNG: GRID ===
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

$stmt->close();
$connect->close();
?>