<?php

class Product {
    private $conn;
    private $table = "tbl_sanpham";

    public function __construct($db) {
        $this->conn = $db;
    }

    // ===== Sinh mã sản phẩm mới tự động =====
    private function generateMaSP() {
        $sql = "SELECT MaSP FROM $this->table ORDER BY MaSP DESC LIMIT 1";
        $result = $this->conn->query($sql);

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $lastId = (int) substr($row['MaSP'], 2); // bỏ "SP"
            $newId = 'SP' . str_pad($lastId + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newId = 'SP001';
        }
        return $newId;
    }

    // ===== 1. Thêm sản phẩm =====
    public function add($TenSP, $MaDM, $DonGia, $MoTa, $TrangThai, $HinhAnh) {
        $MaSP = $this->generateMaSP(); // tự sinh mã

        $sql = "INSERT INTO $this->table (MaSP, TenSP, MaDM, DonGia, MoTa, TrangThai, HinhAnh)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sssdsbs", $MaSP, $TenSP, $MaDM, $DonGia, $MoTa, $TrangThai, $HinhAnh);
        if ($stmt->execute()) {
            return $MaSP; // trả về mã sản phẩm mới để client biết
        }
        return false;
    }

    // ===== 2. Lấy tất cả sản phẩm =====
    public function getAll() {
        $sql = "SELECT sp.MaSP, sp.TenSP, sp.MaDM, dm.TenDM, sp.DonGia, sp.MoTa, sp.TrangThai, sp.HinhAnh
                FROM $this->table sp
                LEFT JOIN tbl_danhmuc dm ON sp.MaDM = dm.MaDM
                ORDER BY sp.MaSP ASC";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // ===== 3. Lấy 1 sản phẩm theo mã =====
    public function getOne($MaSP) {
        $sql = "SELECT sp.MaSP, sp.TenSP, sp.MaDM, dm.TenDM, sp.DonGia, sp.MoTa, sp.TrangThai, sp.HinhAnh
                FROM $this->table sp
                LEFT JOIN tbl_danhmuc dm ON sp.MaDM = dm.MaDM
                WHERE sp.MaSP = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $MaSP);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    // ===== 4. Cập nhật sản phẩm =====
    public function update($MaSP, $TenSP, $MaDM, $DonGia, $MoTa, $TrangThai, $HinhAnh) {
        $sql = "UPDATE $this->table 
                SET TenSP = ?, MaDM = ?, DonGia = ?, MoTa = ?, TrangThai = ?, HinhAnh = ?
                WHERE MaSP = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssdssss", $TenSP, $MaDM, $DonGia, $MoTa, $TrangThai, $HinhAnh, $MaSP);
        return $stmt->execute();
    }

    // ===== 5. Xóa sản phẩm =====
    public function delete($MaSP) {
        $sql = "DELETE FROM $this->table WHERE MaSP = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $MaSP);
        return $stmt->execute();
    }

    // ===== 6. Kiểm tra tồn tại sản phẩm =====
    public function exists($MaSP) {
        $sql = "SELECT COUNT(*) as total FROM $this->table WHERE MaSP = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $MaSP);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['total'] > 0;
    }

    // ===== 7. Tìm sản phẩm theo tên =====
    public function searchByName($keyword) {
        $sql = "SELECT sp.MaSP, sp.TenSP, sp.MaDM, dm.TenDM, sp.DonGia, sp.MoTa, sp.TrangThai, sp.HinhAnh
                FROM $this->table sp
                LEFT JOIN tbl_danhmuc dm ON sp.MaDM = dm.MaDM
                WHERE sp.TenSP LIKE ?";
        $stmt = $this->conn->prepare($sql);
        $likeKeyword = "%" . $keyword . "%";
        $stmt->bind_param("s", $likeKeyword);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // ===== 8. Tìm sản phẩm theo mã =====
    public function searchByCode($MaSP) {
        $sql = "SELECT sp.MaSP, sp.TenSP, sp.MaDM, dm.TenDM, sp.DonGia, sp.MoTa, sp.TrangThai, sp.HinhAnh
                FROM $this->table sp
                LEFT JOIN tbl_danhmuc dm ON sp.MaDM = dm.MaDM
                WHERE sp.MaSP LIKE ?";
        $stmt = $this->conn->prepare($sql);
        $likeCode = "%" . $MaSP . "%";
        $stmt->bind_param("s", $likeCode);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // ===== 9. Đếm tổng số sản phẩm =====
    public function count() {
        $sql = "SELECT COUNT(*) as total FROM $this->table";
        $result = $this->conn->query($sql);
        $row = $result->fetch_assoc();
        return (int)$row['total'];
    }

    // ===== 10. Phân trang =====
    public function paginate($limit, $offset) {
        $sql = "SELECT sp.MaSP, sp.TenSP, sp.MaDM, dm.TenDM, sp.DonGia, sp.MoTa, sp.TrangThai, sp.HinhAnh
                FROM $this->table sp
                LEFT JOIN tbl_danhmuc dm ON sp.MaDM = dm.MaDM
                ORDER BY sp.MaSP ASC
                LIMIT ? OFFSET ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    // ===== 11. Lọc sản phẩm =====
    public function filter($MaDM = null, $minPrice = null, $maxPrice = null) {
        $sql = "SELECT sp.*, dm.TenDM
                FROM $this->table sp
                LEFT JOIN tbl_danhmuc dm ON sp.MaDM = dm.MaDM
                WHERE 1=1";
        $params = [];
        $types = "";

        if (!empty($MaDM)) {
            $sql .= " AND sp.MaDM = ?";
            $params[] = $MaDM;
            $types .= "s";
        }
        if (!empty($minPrice)) {
            $sql .= " AND sp.DonGia >= ?";
            $params[] = $minPrice;
            $types .= "d";
        }
        if (!empty($maxPrice)) {
            $sql .= " AND sp.DonGia <= ?";
            $params[] = $maxPrice;
            $types .= "d";
        }

        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // ===== 12. Sắp xếp sản phẩm =====
    public function sortByPrice($order = 'asc') {
        $order = strtolower($order) === 'desc' ? 'DESC' : 'ASC';
        $sql = "SELECT sp.*, dm.TenDM 
                FROM $this->table sp
                LEFT JOIN tbl_danhmuc dm ON sp.MaDM = dm.MaDM
                ORDER BY sp.DonGia $order";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>
