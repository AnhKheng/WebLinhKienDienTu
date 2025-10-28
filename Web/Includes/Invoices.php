
<?php
class HoaDon {
    private $conn;
    private $table = "tbl_hoadonban";

    public function __construct($db) {
        $this->conn = $db;
    }

    // 🧾 Thêm hóa đơn mới
    public function add($MaHD, $NgayBan, $MaNV, $MaKH, $MaCH, $TongTien) {
        $sql = "INSERT INTO $this->table (MaHD, NgayBan, MaNV, MaKH, MaCH, TongTien)
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sssssf", $MaHD, $NgayBan, $MaNV, $MaKH, $MaCH, $TongTien);
        return $stmt->execute();
    }

    // 📋 Lấy toàn bộ hóa đơn (join tên nhân viên, khách hàng, cửa hàng)
    public function getAll() {
        $sql = "SELECT hd.MaHD, hd.NgayBan, hd.MaNV,  
                       hd.MaKH,
                       hd.MaCH, 
                       hd.TongTien
                FROM tbl_hoadonban hd ";
                // -- LEFT JOIN tbl_nhanvien nv ON hd.MaNV = nv.MaNV
                // -- LEFT JOIN tbl_khachhang kh ON hd.MaKH = kh.MaKH
                // -- LEFT JOIN tbl_cuahang ch ON hd.MaCH = ch.MaCH
                // -- ORDER BY hd.NgayBan DESC";
                
        $result = $this->conn->query($sql);
        return $result;
    }

    // 🔍 Lấy thông tin chi tiết 1 hóa đơn
    // public function getById($MaHD) {
    //     $sql = "SELECT * FROM $this->table WHERE MaHD = ?";
    //     $stmt = $this->conn->prepare($sql);
    //     $stmt->bind_param("s", $MaHD);
    //     $stmt->execute();
    //     $result = $stmt->get_result();
    //     return $result->fetch_assoc();
    // }

    // ✏️ Cập nhật thông tin hóa đơn
    public function update($MaHD, $NgayBan, $MaNV, $MaKH, $MaCH, $TongTien) {
        $sql = "UPDATE $this->table 
                SET NgayBan = ?, MaNV = ?, MaKH = ?, MaCH = ?, TongTien = ?
                WHERE MaHD = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssssfs", $NgayBan, $MaNV, $MaKH, $MaCH, $TongTien, $MaHD);
        return $stmt->execute();
    }

    // 🗑️ Xóa hóa đơn
    public function delete($MaHD) {
        $sql = "DELETE FROM $this->table WHERE MaHD = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $MaHD);
        return $stmt->execute();
    }
}
?>
