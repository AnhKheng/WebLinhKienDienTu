<?php
// 1. Kiểm tra đăng nhập
if (!isset($_SESSION['MaKH'])) {
    echo "<h2>Vui lòng đăng nhập để xem lịch sử mua hàng.</h2>";
    exit;
}

// 2. Bao gồm các file cần thiết (Config và API)
include_once '../../API/Config/db_config.php';
include_once '../../API/client/Order/Order_api.php'; 

$maKH = $_SESSION['MaKH']; 

// 3. Khởi tạo BUS và gọi hàm lấy dữ liệu
$orderBUS = new OrderBUS($connect);
$don_hangs = $orderBUS->getOrderHistory($maKH);
?>

<link rel="stylesheet" href="assets/css/cart.css">
<link rel="stylesheet" href="assets/css/order_history.css">

<h2 style="text-align:center; margin-top: 20px;">Lịch sử mua hàng của bạn</h2>

<div class="order-history-list">
    <?php if (!empty($don_hangs)): ?>
        <?php foreach ($don_hangs as $maHD => $order): ?>
            
            <div class="order-summary-header" id="header-<?php echo htmlspecialchars($maHD); ?>" onclick="toggleOrderDetails('details-<?php echo htmlspecialchars($maHD); ?>', this)">
                <div class="order-info">
                    <h3>Đơn hàng: <?php echo htmlspecialchars($maHD); ?></h3>
                    <span>Ngày: <?php echo date("d/m/Y", strtotime($order['NgayBan'])); ?></span> 
                    
                    <?php
                        $status_text = $order['TrangThaiXuLy'];
                        $status_class = ($status_text == 'Đã xử lý') ? 'processed' : 'pending';
                    ?>
                    <span class="order-status <?php echo $status_class; ?>">
                        <?php echo htmlspecialchars($status_text); ?>
                    </span>
                </div>
                
                <div class="order-total">
                    Tổng: <?php echo number_format($order['TongTienDonHang']); ?> đ
                </div>

                <span class="toggle-icon">[+]</span>
            </div>

            <div class="order-details-content" id="details-<?php echo htmlspecialchars($maHD); ?>">
                <table style="width:100%; border-collapse:collapse; text-align:center; margin-top: 0;">
                    <tr>
                        <th width=130px>Hình ảnh</th>
                        <th>Tên sản phẩm</th>
                        <th width=130px>Giá bán</th>
                        <th width=130px>Số lượng</th>
                        <th width=130px>Thành tiền</th>
                    </tr>
                    
                    <?php foreach ($order['details'] as $item): ?>
                        <?php
                            $hinhAnh = $item['HinhAnh'];
                            if (strpos($hinhAnh, '/') === false) {
                                $hinhAnh = "../../Public/img/" . $hinhAnh;
                            }
                        ?>
                        <tr>
                            <td><img src="<?php echo htmlspecialchars($hinhAnh); ?>" width="130" height="130"></td>
                            <td><?php echo htmlspecialchars($item['TenSP']); ?></td>
                            <td><?php echo number_format($item['DonGia']); ?> đ</td>
                            <td><?php echo htmlspecialchars($item['SoLuong']); ?></td>
                            <td><?php echo number_format($item['ThanhTien']); ?> đ</td>
                        </tr>
                    <?php endforeach; ?>

                    <tr>
                        <td colspan="4" style="text-align:right;">Tổng cộng đơn hàng:</td>
                        <td><strong><?php echo number_format($order['TongTienDonHang']); ?> đ</strong></td>
                    </tr>
                </table>
            </div>

        <?php endforeach; ?>
    <?php else: ?>
        <p style='text-align:center;'>Bạn chưa có đơn hàng nào.</p>
    <?php endif; ?>
</div>

<script src="assets/js/order_history.js"></script>