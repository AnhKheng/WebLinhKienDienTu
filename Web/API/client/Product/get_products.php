<?php
// Gọi file cấu hình DB (giả sử đường dẫn tương đối từ ajax_load_products.php)
include_once '../../../API/Config/db_config.php';

// Lấy tham số từ GET (từ AJAX)
$category = isset($_GET['category']) ? trim($_GET['category']) : '';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Xây dựng query
$sql = "SELECT MaSP, TenSP, DonGia, HinhAnh FROM tbl_sanpham WHERE 1=1";
if (!empty($category)) {
    $sql .= " AND MaDM = '" . $connect->real_escape_string($category) . "'";
}
if (!empty($search)) {
    $sql .= " AND TenSP LIKE '%" . $connect->real_escape_string($search) . "%'";
}

// Thực thi query
$result = $connect->query($sql);

if (!$result) {
    echo "<p>Lỗi truy vấn: " . $connect->error . "</p>";
    exit;
}

// Bắt đầu output HTML (chỉ phần product-grid, không bao gồm h2 để tránh trùng lặp)
echo '<div class="product-grid">';
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $hinh = !empty($row['HinhAnh']) 
            ? '../img/' . $row['HinhAnh'] 
            : '../img/default_product.png';
        echo '
        <div class="product-card">
            <img src="' . htmlspecialchars($hinh) . '" alt="' . htmlspecialchars($row['TenSP']) . '">
            <h3>' . htmlspecialchars($row['TenSP']) . '</h3>
            <p class="price">' . number_format($row['DonGia'], 0, ',', '.') . '₫</p>
            <button class="btn-buy">Mua ngay</button>
        </div>';
    }
} else {
    echo "<p>Không có sản phẩm nào phù hợp.</p>";
}
echo '</div>';
?>