<?php
class Stores {
    private $connect;

    public function __construct($db) {
        $this->connect = $db;
    }

    // Lấy danh sách tất cả cửa hàng
    public function getAll() {
        $sql = "SELECT MaCH, TenCH, DiaChi, SoDienThoai FROM tbl_cuahang";

        $stmt = $this->connect->prepare($sql);
        if (!$stmt) {
            // In lỗi SQL để dễ tìm
            return ["status" => "error", "message" => "Lỗi prepare(): " . $this->connect->error];
        }

        if (!$stmt->execute()) {
            return ["status" => "error", "message" => "Lỗi execute(): " . $stmt->error];
        }

        $result = $stmt->get_result();
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        return ["status" => "success", "data" => $data];
    }

    // Lấy cửa hàng theo mã
    public function getById($maCH) {
        $sql = "SELECT MaCH, TenCH, DiaChi FROM tbl_cuahang WHERE MaCH = ?";
        $stmt = $this->connect->prepare($sql);
        if (!$stmt) {
            return ["status" => "error", "message" => "Lỗi prepare(): " . $this->connect->error];
        }

        $stmt->bind_param("s", $maCH);
        if (!$stmt->execute()) {
            return ["status" => "error", "message" => "Lỗi execute(): " . $stmt->error];
        }

        $result = $stmt->get_result();
        $data = $result->fetch_assoc();

        return $data ? ["status" => "success", "data" => $data] :
                       ["status" => "error", "message" => "Không tìm thấy cửa hàng!"];
    }

    // 🔹 Thêm cửa hàng
    // 🔹 Thêm cửa hàng với MaCH tự động
public function add($tenCH, $diaChi, $sdt) {
    // 1️⃣ Lấy mã cửa hàng cuối cùng
    $sqlLast = "SELECT MaCH FROM tbl_cuahang ORDER BY MaCH DESC LIMIT 1";
    $stmtLast = $this->connect->prepare($sqlLast);
    if (!$stmtLast) return ["status" => "error", "message" => $this->connect->error];
    $stmtLast->execute();
    $last = $stmtLast->get_result()->fetch_assoc();
    $lastMa = $last['MaCH'] ?? 'CH000';

    // 2️⃣ Sinh mã mới
    $num = intval(substr($lastMa, 2)) + 1;
    $maCH = 'CH' . str_pad($num, 3, '0', STR_PAD_LEFT);

    // 3️⃣ Insert
    $sql = "INSERT INTO tbl_cuahang (MaCH, TenCH, DiaChi, SoDienThoai) VALUES (?, ?, ?, ?)";
    $stmt = $this->connect->prepare($sql);
    if (!$stmt) return ["status" => "error", "message" => $this->connect->error];

    $stmt->bind_param("ssss", $maCH, $tenCH, $diaChi, $sdt);
    if (!$stmt->execute()) return ["status" => "error", "message" => $stmt->error];

    return ["status" => "success", "message" => "Thêm cửa hàng thành công!", "MaCH" => $maCH];
}


    // 🔹 Cập nhật cửa hàng
    public function update($maCH, $tenCH, $diaChi, $sdt) {
        $sql = "UPDATE tbl_cuahang SET TenCH = ?, DiaChi = ?, SoDienThoai = ? WHERE MaCH = ?";
        $stmt = $this->connect->prepare($sql);
        if (!$stmt) return ["status" => "error", "message" => $this->connect->error];
        $stmt->bind_param("ssss", $tenCH, $diaChi, $sdt, $maCH);
        if (!$stmt->execute()) return ["status" => "error", "message" => $stmt->error];
        return ["status" => "success", "message" => "Cập nhật cửa hàng thành công!"];
    }

    // 🔹 Xóa cửa hàng
    public function delete($maCH) {
        $sql = "DELETE FROM tbl_cuahang WHERE MaCH = ?";
        $stmt = $this->connect->prepare($sql);
        if (!$stmt) return ["status" => "error", "message" => $this->connect->error];
        $stmt->bind_param("s", $maCH);
        if (!$stmt->execute()) return ["status" => "error", "message" => $stmt->error];
        return ["status" => "success", "message" => "Xóa cửa hàng thành công!"];
    }
}

?>
