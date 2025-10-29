<?php
// Gọi kết nối từ index (index đã include db_config.php)
$sql = "SELECT MaSP, TenSP, DonGia, HinhAnh FROM tbl_sanpham";
$result = $connect->query($sql);

if (!$result) {
    die("Không thể truy vấn dữ liệu: " . $connect->error);
}
?>

<div class="product-section">
    <h2>Danh sách sản phẩm</h2>
    <div class="product-grid">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // Nếu ảnh trong DB chỉ lưu tên file (vd: "lap1.png")
                // thì nối thêm đường dẫn "assets/img/"
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
            echo "<p>Không có sản phẩm nào.</p>";
        }
        ?>
    </div>
</div>
