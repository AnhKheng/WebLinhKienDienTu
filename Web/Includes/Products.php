<?php
class Product {
    private $conn;
    private $table = "tbl_sanpham";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function add($MaSP, $TenSP, $MaDM, $DonGia, $MoTa, $TrangThai, $HinhAnh) {
        $sql = "INSERT INTO $this->table (MaSP, TenSP, MaDM, DonGia, MoTa, TrangThai, HinhAnh)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sssdsbs", $MaSP, $TenSP, $MaDM, $DonGia, $MoTa, $TrangThai, $HinhAnh);
        return $stmt->execute();
    }

    // 🆕 Lấy toàn bộ sản phẩm
    public function getAll() {
        $sql = "SELECT sp.MaSP, sp.TenSP, sp.MaDM, dm.TenDM, sp.DonGia, sp.MoTa, sp.TrangThai, sp.HinhAnh
                FROM tbl_sanpham sp
                LEFT JOIN tbl_danhmuc dm ON sp.MaDM = dm.MaDM
                ORDER BY sp.MaSP ASC";
        $result = $this->conn->query($sql);
        return $result;
    }
}
?>
