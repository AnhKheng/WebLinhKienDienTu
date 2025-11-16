<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Xử lý tất cả logic nghiệp vụ liên quan đến đơn hàng của khách hàng.
class OrderBUS {
    private $connect;

    // Khởi tạo với kết nối database.
    public function __construct($db_connection) {
        $this->connect = $db_connection;
    }

    // Lấy lịch sử đơn hàng đã được gom nhóm cho một khách hàng.
    public function getOrderHistory($maKH) {
        
        // 1. Truy vấn TẤT CẢ sản phẩm của khách hàng...
        $sql = "SELECT
                    hd.MaHD,
                    hd.NgayBan, 
                    sp.HinhAnh,
                    sp.TenSP,
                    cthd.DonGia,
                    cthd.SoLuong,
                    (cthd.DonGia * cthd.SoLuong) AS ThanhTien,
                    CASE
                        WHEN hd.MaNV IS NULL THEN 'Đang xử lý'
                        ELSE 'Đã xử lý'
                    END AS TrangThaiXuLy
                FROM
                    tbl_chitiethoadon AS cthd
                JOIN
                    tbl_sanpham AS sp ON cthd.MaSP = sp.MaSP
                JOIN
                    tbl_hoadonban AS hd ON cthd.MaHD = hd.MaHD
                WHERE
                    hd.MaKH = ?
                ORDER BY
                    hd.NgayBan DESC, hd.MaHD
                ";

        $stmt = $this->connect->prepare($sql);

        if ($stmt === false) {
            error_log("Lỗi SQL prepare: " . $this->connect->error); 
            return []; 
        }

        $stmt->bind_param('s', $maKH); 
        $stmt->execute();
        $result = $stmt->get_result();

        // 2. Gom nhóm sản phẩm và tính tổng tiền cho từng đơn hàng
        $don_hangs = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $maHD = $row['MaHD'];

                if (!isset($don_hangs[$maHD])) {
                    $don_hangs[$maHD] = [
                        'details'       => [], 
                        'NgayBan'       => $row['NgayBan'], // Sử dụng key 'NgayBan'
                        'TrangThaiXuLy' => $row['TrangThaiXuLy'],
                        'TongTienDonHang' => 0 
                    ];
                }

                $don_hangs[$maHD]['details'][] = $row;
                $don_hangs[$maHD]['TongTienDonHang'] += $row['ThanhTien'];
            }
        }
        $stmt->close();
        
        // 3. Trả về mảng dữ liệu đã xử lý
        return $don_hangs;
    }
}
?>