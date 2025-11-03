<?php
class Customers {
    private $conn;
    private $table = "tbl_khachhang";

    public function __construct($db) {
        $this->conn = $db;
    }

    // ==== 1️⃣ LẤY TOÀN BỘ KHÁCH HÀNG ====
    public function getAll() {
        $sql = "SELECT MaKH, TenKH, SoDienThoai, DiaChi FROM {$this->table}";
        $result = $this->conn->query($sql);

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }

    // ==== 2️⃣ LẤY KHÁCH HÀNG THEO MÃ ====
    public function getById($MaKH) {
        $stmt = $this->conn->prepare("SELECT MaKH, TenKH, SoDienThoai, DiaChi FROM {$this->table} WHERE MaKH = ?");
        $stmt->bind_param("s", $MaKH);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc() ?: ["status" => "error", "message" => "Không tìm thấy khách hàng!"];
    }

    // ==== 3️⃣ THÊM KHÁCH HÀNG MỚI ====
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
}
?>
