<?php

class Category {
    private $conn;
    private $table = "tbl_danhmuc";

    public function __construct($db) {
        $this->conn = $db;
    }

    // 🔹 Hàm sinh mã tự động: DM01, DM02, ...
    private function generateMaDM() {
        $sql = "SELECT MaDM FROM $this->table ORDER BY MaDM DESC LIMIT 1";
        $result = $this->conn->query($sql);

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $lastId = (int) substr($row['MaDM'], 2); // cắt bỏ 'DM'
            $newId = 'DM' . str_pad($lastId + 1, 2, '0', STR_PAD_LEFT);
        } else {
            $newId = 'DM01';
        }
        return $newId;
    }

    // 🔹 Thêm danh mục mới (tự tạo mã)
    public function add($TenDM) {
        $MaDM = $this->generateMaDM();
        $sql = "INSERT INTO $this->table (MaDM, TenDM) VALUES (?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $MaDM, $TenDM);
        return $stmt->execute();
    }

    // 🔹 Lấy tất cả danh mục
    public function getAll() {
        $sql = "SELECT MaDM, TenDM FROM $this->table";
        $result = $this->conn->query($sql);
        $data = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        return $data;
    }

    // 🔹 Lấy 1 danh mục theo mã
    public function getOne($MaDM) {
        $stmt = $this->conn->prepare("SELECT MaDM, TenDM FROM $this->table WHERE MaDM = ?");
        $stmt->bind_param("s", $MaDM);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    // 🔹 Cập nhật danh mục
    public function update($MaDM, $TenDM) {
        $sql = "UPDATE $this->table SET TenDM = ? WHERE MaDM = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $TenDM, $MaDM);
        return $stmt->execute();
    }

    // 🔹 Xóa danh mục
    public function delete($MaDM) {
        $sql = "DELETE FROM $this->table WHERE MaDM = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $MaDM);
        return $stmt->execute();
    }

    // 🔹 Kiểm tra tồn tại
    public function exists($MaDM) {
        $sql = "SELECT COUNT(*) as total FROM $this->table WHERE MaDM = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $MaDM);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['total'] > 0;
    }

    // 🔹 Tìm kiếm theo tên
    public function search($keyword) {
        $sql = "SELECT MaDM, TenDM FROM $this->table WHERE TenDM LIKE ?";
        $stmt = $this->conn->prepare($sql);
        $likeKeyword = "%" . $keyword . "%";
        $stmt->bind_param("s", $likeKeyword);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // 🔹 Đếm tổng số danh mục
    public function count() {
        $sql = "SELECT COUNT(*) as total FROM $this->table";
        $result = $this->conn->query($sql);
        $row = $result->fetch_assoc();
        return (int)$row['total'];
    }
}
?>
