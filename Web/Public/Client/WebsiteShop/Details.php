<?php
// Lấy ID sản phẩm từ URL mà Index.php đã nhận
$product_id = isset($_GET['id']) ? htmlspecialchars($_GET['id']) : '';

if (empty($product_id)) {
    echo '<p style="color:red; text-align:center;">Không tìm thấy mã sản phẩm.</p>';
} else {
    // Tạo một div "khung chờ" với data-id
    // File loadDetails.js sẽ tìm div này và lấp đầy nội dung
    echo '<div id="product-detail-container" data-id="' . $product_id . '">';
    echo '  <p style="padding:20px;text-align:center;">Đang tải chi tiết sản phẩm...</p>';
    echo '</div>';
}
?>