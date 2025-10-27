<?php
class Category {
    private $conn;
    private $table = "tbl_danhmuc";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function add($MaDM, $TenDM) {
        $sql = "INSERT INTO $this->table (MaDM, TenDM)
                VALUES (?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $MaDM, $TenDM);
        return $stmt->execute();
    }

    // 🆕 Lấy toàn bộ sản phẩm
    public function getAll() {
        $sql = "SELECT dm.MaDM, dm.TenDM
                FROM tbl_danhmuc dm";
        $result = $this->conn->query($sql);
        return $result;
    }
}
?>
