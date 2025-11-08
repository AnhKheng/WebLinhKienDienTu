<?php
class Employee {
    private $conn;
    private $table = "tbl_nhanvien";

    public function __construct($db) {
        $this->conn = $db;
    }

    // ===== 1. Sinh mã nhân viên tự động =====
    private function generateMaNV() {
        $sql = "SELECT MaNV 
                FROM $this->table 
                ORDER BY CAST(SUBSTRING(MaNV, 3) AS UNSIGNED) DESC 
                LIMIT 1";
        $result = $this->conn->query($sql);

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $lastId = (int) substr($row['MaNV'], 2);
            $newId = 'NV' . str_pad($lastId + 1, 2, '0', STR_PAD_LEFT);
        } else {
            $newId = 'NV01';
        }

        return $newId;
    }

    // ===== 2. Thêm nhân viên =====
    public function add($TenNV, $GioiTinh, $NgaySinh, $SoDienThoai, $MaCH) {
        $MaNV = $this->generateMaNV();

        $sql = "INSERT INTO $this->table 
                (MaNV, TenNV, GioiTinh, NgaySinh, SoDienThoai, MaCH)
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssssss", $MaNV, $TenNV, $GioiTinh, $NgaySinh, $SoDienThoai, $MaCH);

        if ($stmt->execute()) {
            return $MaNV;
        }

        error_log("SQL Error (add employee): " . $stmt->error);
        return false;
    }

    // ===== 3. Lấy tất cả nhân viên =====
    public function getAll() {
        $sql = "SELECT nv.MaNV, nv.TenNV, nv.GioiTinh, nv.NgaySinh, nv.SoDienThoai, nv.MaCH, ch.TenCH
                FROM $this->table nv
                LEFT JOIN tbl_cuahang ch ON nv.MaCH = ch.MaCH
                ORDER BY nv.MaNV ASC";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // ===== 4. Lấy 1 nhân viên theo mã =====
    public function getOne($MaNV) {
        $sql = "SELECT nv.MaNV, nv.TenNV, nv.GioiTinh, nv.NgaySinh, nv.SoDienThoai, nv.MaCH, ch.TenCH
                FROM $this->table nv
                LEFT JOIN tbl_cuahang ch ON nv.MaCH = ch.MaCH
                WHERE nv.MaNV = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $MaNV);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    // ===== 5. Cập nhật nhân viên =====
    public function update($MaNV, $TenNV, $GioiTinh, $NgaySinh, $SoDienThoai, $MaCH) {
        $sql = "UPDATE $this->table 
                SET TenNV = ?, GioiTinh = ?, NgaySinh = ?, SoDienThoai = ?, MaCH = ?
                WHERE MaNV = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssssss", $TenNV, $GioiTinh, $NgaySinh, $SoDienThoai, $MaCH, $MaNV);
        return $stmt->execute();
    }

    // ===== 6. Xóa nhân viên =====
    public function delete($MaNV) {
        $sql = "DELETE FROM $this->table WHERE MaNV = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $MaNV);
        return $stmt->execute();
    }

    // ===== 7. Kiểm tra tồn tại nhân viên =====
    public function exists($MaNV) {
        $sql = "SELECT COUNT(*) as total FROM $this->table WHERE MaNV = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $MaNV);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['total'] > 0;
    }

    // ===== 8. Tìm kiếm theo tên =====
    public function search($keyword) {
        $sql = "SELECT * FROM $this->table 
                WHERE MaNV LIKE :kw OR TenNV LIKE :kw";
        $stmt = $this->conn->prepare($sql);
        $kw = "%$keyword%";
        $stmt->bindParam(":kw", $kw);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ===== 9. Tìm kiếm theo mã =====
    public function searchByCode($MaNV) {
        $sql = "SELECT nv.MaNV, nv.TenNV, nv.GioiTinh, nv.NgaySinh, nv.SoDienThoai, nv.MaCH, ch.TenCH
                FROM $this->table nv
                LEFT JOIN tbl_cuahang ch ON nv.MaCH = ch.MaCH
                WHERE nv.MaNV LIKE ?";
        $stmt = $this->conn->prepare($sql);
        $likeCode = "%" . $MaNV . "%";
        $stmt->bind_param("s", $likeCode);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // ===== 10. Đếm tổng số nhân viên =====
    public function count() {
        $sql = "SELECT COUNT(*) as total FROM $this->table";
        $result = $this->conn->query($sql);
        $row = $result->fetch_assoc();
        return (int)$row['total'];
    }

    // ===== 11. Phân trang =====
    public function paginate($limit, $offset) {
        $sql = "SELECT nv.MaNV, nv.TenNV, nv.GioiTinh, nv.NgaySinh, nv.SoDienThoai, nv.MaCH, ch.TenCH
                FROM $this->table nv
                LEFT JOIN tbl_cuahang ch ON nv.MaCH = ch.MaCH
                ORDER BY nv.MaNV ASC
                LIMIT ? OFFSET ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // ===== 12. Lọc nhân viên =====
    public function filter($MaCH = null, $GioiTinh = null) {
        $sql = "SELECT nv.*, ch.TenCH 
                FROM $this->table nv
                LEFT JOIN tbl_cuahang ch ON nv.MaCH = ch.MaCH
                WHERE 1=1";
        $params = [];
        $types = "";

        if (!empty($MaCH)) {
            $sql .= " AND nv.MaCH = ?";
            $params[] = $MaCH;
            $types .= "s";
        }
        if (!empty($GioiTinh)) {
            $sql .= " AND nv.GioiTinh = ?";
            $params[] = $GioiTinh;
            $types .= "s";
        }

        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
?>
