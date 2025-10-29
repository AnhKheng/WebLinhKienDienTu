<?php
class Category {
    private $conn;
    private $table = "tbl_danhmuc";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function add($MaDM, $TenDM) {
        $sql = "INSERT INTO $this->table (MaDM, TenDM) VALUES (?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $MaDM, $TenDM);
        return $stmt->execute();
    }

    public function getAll() {
        $sql = "SELECT MaDM, TenDM FROM $this->table";
        $result = $this->conn->query($sql);
        return $result;
    }


    public function getOne($MaDM) {
        $sql = "SELECT MaDM, TenDM FROM $this->table WHERE MaDM = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $MaDM);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc(); 
    }
    public function update($MaDM, $TenDM) {
        $sql = "UPDATE $this->table SET TenDM = ? WHERE MaDM = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $TenDM, $MaDM);
        return $stmt->execute();
    }
    public function delete($MaDM) {
        $sql = "DELETE FROM $this->table WHERE MaDM = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $MaDM);
        return $stmt->execute();
    }

    public function exists($MaDM) {
        $sql = "SELECT COUNT(*) as total FROM $this->table WHERE MaDM = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $MaDM);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['total'] > 0;
    }
    public function search($keyword) {
        $sql = "SELECT MaDM, TenDM FROM $this->table WHERE TenDM LIKE ?";
        $stmt = $this->conn->prepare($sql);
        $likeKeyword = "%" . $keyword . "%";
        $stmt->bind_param("s", $likeKeyword);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    public function count() {
        $sql = "SELECT COUNT(*) as total FROM $this->table";
        $result = $this->conn->query($sql);
        $row = $result->fetch_assoc();
        return (int)$row['total'];
    }
}
?>
