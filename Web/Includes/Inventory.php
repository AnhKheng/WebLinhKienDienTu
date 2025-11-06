<?php
class Inventory {
    private $conn;
    private $table = "tbl_kho";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $sql = "SELECT 
                    k.MaCH, c.TenCH, 
                    k.MaSP, s.TenSP, 
                    k.SoLuongTon
                FROM $this->table AS k
                INNER JOIN tbl_cuahang AS c ON k.MaCH = c.MaCH
                INNER JOIN tbl_sanpham AS s ON k.MaSP = s.MaSP";
        $result = $this->conn->query($sql);
        $data = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        return $data;
    }

    public function getByStore($MaCH) {
        $sql = "SELECT 
                    k.MaCH, c.TenCH, 
                    k.MaSP, s.TenSP, 
                    k.SoLuongTon
                FROM $this->table AS k
                INNER JOIN tbl_cuahang AS c ON k.MaCH = c.MaCH
                INNER JOIN tbl_sanpham AS s ON k.MaSP = s.MaSP
                WHERE k.MaCH = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $MaCH);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getByProduct($MaSP) {
        $sql = "SELECT 
                    k.MaCH, c.TenCH, 
                    k.MaSP, s.TenSP, 
                    k.SoLuongTon
                FROM $this->table AS k
                INNER JOIN tbl_cuahang AS c ON k.MaCH = c.MaCH
                INNER JOIN tbl_sanpham AS s ON k.MaSP = s.MaSP
                WHERE k.MaSP = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $MaSP);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getOne($MaCH, $MaSP) {
        $sql = "SELECT 
                    k.MaCH, c.TenCH, 
                    k.MaSP, s.TenSP, 
                    k.SoLuongTon
                FROM $this->table AS k
                INNER JOIN tbl_cuahang AS c ON k.MaCH = c.MaCH
                INNER JOIN tbl_sanpham AS s ON k.MaSP = s.MaSP
                WHERE k.MaCH = ? AND k.MaSP = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $MaCH, $MaSP);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function countByStore($MaCH) {
        $sql = "SELECT c.TenCH, SUM(k.SoLuongTon) as total
                FROM $this->table AS k
                INNER JOIN tbl_cuahang AS c ON k.MaCH = c.MaCH
                WHERE k.MaCH = ?
                GROUP BY c.TenCH";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $MaCH);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result ?: ["TenCH" => null, "total" => 0];
    }
    public function getPaged($offset, $limit) {
        $sql = "SELECT 
                    k.MaCH, c.TenCH, 
                    k.MaSP, s.TenSP, 
                    k.SoLuongTon
                FROM $this->table AS k
                INNER JOIN tbl_cuahang AS c ON k.MaCH = c.MaCH
                INNER JOIN tbl_sanpham AS s ON k.MaSP = s.MaSP
                LIMIT ?, ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $offset, $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function countAll() {
        $sql = "SELECT COUNT(*) as total FROM $this->table";
        $result = $this->conn->query($sql);
        $row = $result->fetch_assoc();
        return (int)$row['total'];
    }
        public function searchInventory($keyword, $offset, $limit) {
        $keyword = "%{$keyword}%";
        $sql = "SELECT 
                    k.MaCH, c.TenCH, 
                    k.MaSP, s.TenSP, 
                    k.SoLuongTon
                FROM $this->table AS k
                INNER JOIN tbl_cuahang AS c ON k.MaCH = c.MaCH
                INNER JOIN tbl_sanpham AS s ON k.MaSP = s.MaSP
                WHERE c.TenCH LIKE ? OR s.TenSP LIKE ?
                LIMIT ?, ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssii", $keyword, $keyword, $offset, $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>
