<?php
class Customers {
    private $conn;
    private $table = "tbl_khachhang";

    public function __construct($db) {
        $this->conn = $db;
    }
    public function getAll() {
        $sql = "SELECT MaKH, TenKH, SoDienThoai, DiaChi FROM {$this->table}";
        $result = $this->conn->query($sql);

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }
    public function getById($MaKH) {
        $stmt = $this->conn->prepare("SELECT MaKH, TenKH, SoDienThoai, DiaChi FROM {$this->table} WHERE MaKH = ?");
        $stmt->bind_param("s", $MaKH);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc() ?: ["status" => "error", "message" => "Không tìm thấy khách hàng!"];
    }

    public function add($TenKH, $SoDienThoai, $DiaChi) {
        // Sinh mã KH tự động (KH01, KH02, …)
        $res = $this->conn->query("SELECT MAX(CAST(SUBSTRING(MaKH, 3) AS UNSIGNED)) AS max_id FROM {$this->table}");
        $row = $res->fetch_assoc();
        $nextId = "KH" . str_pad(($row['max_id'] ?? 0) + 1, 2, "0", STR_PAD_LEFT);

        $stmt = $this->conn->prepare("INSERT INTO {$this->table} (MaKH, TenKH, SoDienThoai, DiaChi) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nextId, $TenKH, $SoDienThoai, $DiaChi);

        if ($stmt->execute()) {
            return ["status" => "success", "message" => "Thêm khách hàng thành công!", "MaKH" => $nextId];
        } else {
            return ["status" => "error", "message" => "Không thể thêm khách hàng: " . $stmt->error];
        }
    }
    public function update($MaKH, $TenKH, $SoDienThoai, $DiaChi) {
        $stmt = $this->conn->prepare("UPDATE {$this->table} SET TenKH = ?, SoDienThoai = ?, DiaChi = ? WHERE MaKH = ?");
        $stmt->bind_param("ssss", $TenKH, $SoDienThoai, $DiaChi, $MaKH);

        if ($stmt->execute()) {
            return ["status" => "success", "message" => "Cập nhật khách hàng thành công!"];
        } else {
            return ["status" => "error", "message" => "Không thể cập nhật khách hàng: " . $stmt->error];
        }
    }

    // Xóa khách hàng
    public function delete($MaKH) {
        $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE MaKH = ?");
        $stmt->bind_param("s", $MaKH);

        if ($stmt->execute()) {
            return ["status" => "success", "message" => "Xóa khách hàng thành công!"];
        } else {
            return ["status" => "error", "message" => "Không thể xóa khách hàng: " . $stmt->error];
        }
    }

    // Tìm kiếm khách hàng theo tên
    public function searchByName($TenKH) {
        $like = "%" . $TenKH . "%";
        $stmt = $this->conn->prepare("SELECT MaKH, TenKH, SoDienThoai, DiaChi FROM {$this->table} WHERE TenKH LIKE ?");
        $stmt->bind_param("s", $like);
        $stmt->execute();
        $result = $stmt->get_result();

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        return $data ?: ["status" => "error", "message" => "Không tìm thấy khách hàng!"];
    }
    public function getAllPaged($page = 1, $limit = 10) {
        $offset = ($page - 1) * $limit;
        $sql = "SELECT MaKH, TenKH, SoDienThoai, DiaChi FROM {$this->table} LIMIT ?, ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $offset, $limit);
        $stmt->execute();
        $result = $stmt->get_result();

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        // Lấy tổng số khách hàng để trả về info phân trang
        $totalRes = $this->conn->query("SELECT COUNT(*) as total FROM {$this->table}");
        $total = $totalRes->fetch_assoc()['total'] ?? 0;
        $totalPages = ceil($total / $limit);

        return [
            "data" => $data,
            "page" => $page,
            "limit" => $limit,
            "total" => $total,
            "totalPages" => $totalPages
        ];
    }
    }
?>
