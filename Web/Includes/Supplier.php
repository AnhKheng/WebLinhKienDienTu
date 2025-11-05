<?php
class Supplier {
    private $conn;
    private $table = "tbl_nhacungcap";

    public function __construct($db) {
        $this->conn = $db;
    }
    private function generateMaNCC() {
        $sql = "SELECT MaNCC FROM $this->table ORDER BY MaNCC DESC LIMIT 1";
        $result = $this->conn->query($sql);

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $lastId = (int) substr($row['MaNCC'], 3); // cắt bỏ 'NCC'
            $newId = 'NCC' . str_pad($lastId + 1, 2, '0', STR_PAD_LEFT);
        } else {
            $newId = 'NCC01';
        }
        return $newId;
    }

    public function add($TenNCC, $DiaChi, $SoDienThoai) {
        $MaNCC = $this->generateMaNCC();
        $sql = "INSERT INTO $this->table (MaNCC, TenNCC, DiaChi, SoDienThoai) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssss", $MaNCC, $TenNCC, $DiaChi, $SoDienThoai);
        return $stmt->execute();
    }

    public function getAll() {
        $sql = "SELECT MaNCC, TenNCC, DiaChi, SoDienThoai FROM $this->table";
        $result = $this->conn->query($sql);
        return ($result && $result->num_rows > 0) ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getOne($MaNCC) {
        $stmt = $this->conn->prepare("SELECT MaNCC, TenNCC, DiaChi, SoDienThoai FROM $this->table WHERE MaNCC = ?");
        $stmt->bind_param("s", $MaNCC);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function update($MaNCC, $TenNCC, $DiaChi, $SoDienThoai) {
        $sql = "UPDATE $this->table SET TenNCC = ?, DiaChi = ?, SoDienThoai = ? WHERE MaNCC = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssss", $TenNCC, $DiaChi, $SoDienThoai, $MaNCC);
        return $stmt->execute();
    }

    public function delete($MaNCC) {
        $sql = "DELETE FROM $this->table WHERE MaNCC = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $MaNCC);
        return $stmt->execute();
    }

    public function exists($MaNCC) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM $this->table WHERE MaNCC = ?");
        $stmt->bind_param("s", $MaNCC);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['total'] > 0;
    }

    public function search($keyword) {
        $likeKeyword = "%" . $keyword . "%";
        $stmt = $this->conn->prepare("SELECT MaNCC, TenNCC, DiaChi, SoDienThoai FROM $this->table WHERE TenNCC LIKE ?");
        $stmt->bind_param("s", $likeKeyword);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function count() {
        $result = $this->conn->query("SELECT COUNT(*) as total FROM $this->table");
        $row = $result->fetch_assoc();
        return (int)$row['total'];
    }
}
?>
