<?php
class Dashboard {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Tổng số sản phẩm
    public function countProducts() {
        $sql = "SELECT COUNT(*) AS total FROM tbl_sanpham";
        $res = $this->conn->query($sql);
        if (!$res) return 0;
        $row = $res->fetch_assoc();
        return (int)($row['total'] ?? 0);
    }

    // Tổng số khách hàng
    public function countCustomers() {
        $sql = "SELECT COUNT(*) AS total FROM tbl_khachhang";
        $res = $this->conn->query($sql);
        if (!$res) return 0;
        $row = $res->fetch_assoc();
        return (int)($row['total'] ?? 0);
    }

    // Tổng số hóa đơn
    public function countInvoices() {
        $sql = "SELECT COUNT(*) AS total FROM tbl_hoadon";
        $res = $this->conn->query($sql);
        if (!$res) return 0;
        $row = $res->fetch_assoc();
        return (int)($row['total'] ?? 0);
    }

    // Doanh thu hôm nay (tổng tiền các hóa đơn có ngày = hôm nay)
    public function totalToday() {
        // giả sử trong bảng tbl_hoadon có cột total và created_at (datetime/date)
        $today = date('Y-m-d');
        $sql = "SELECT SUM(total) AS sum_today FROM tbl_hoadon WHERE DATE(created_at) = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) return 0;
        $stmt->bind_param('s', $today);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        return (float)($row['sum_today'] ?? 0);
    }

    // Doanh thu theo tháng (trả về mảng [{month, year, total}, ...])
    public function revenueByMonth($months = 6) {
    // Lấy $months tháng gần nhất (mặc định 6)
        $sql = "
            SELECT 
                MONTH(NgayLap) AS month, 
                YEAR(NgayLap) AS year, 
                SUM(TongTien) AS total
            FROM tbl_hoadon
            WHERE NgayLap >= DATE_SUB(CURDATE(), INTERVAL ? MONTH)
            GROUP BY YEAR(NgayLap), MONTH(NgayLap)
            ORDER BY YEAR(NgayLap), MONTH(NgayLap)
        ";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) return [];
        $stmt->bind_param('i', $months);
        $stmt->execute();
        $res = $stmt->get_result();
        $out = [];
        while ($r = $res->fetch_assoc()) {
            $out[] = [
                'month' => (int)$r['month'],
                'year' => (int)$r['year'],
                'total' => (float)$r['total']
            ];
        }
        return $out;
    }

    
    // Tổng hợp dữ liệu
    public function getDashboardData() {
        return [
            "countProduct" => $this->countProducts(),
            "countCustomer" => $this->countCustomers(),
            "countInvoice" => $this->countInvoices(),
            "totalToday" => $this->totalToday(),
            "revenueByMonth" => $this->revenueByMonth(6)
        ];
    }
}
?>
