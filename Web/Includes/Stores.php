<?php
class Stores {
    private $connect;

    public function __construct($db) {
        $this->connect = $db;
    }

    // Lấy danh sách tất cả cửa hàng
    public function getAll() {
        $sql = "SELECT MaCH, TenCH, DiaChi FROM tbl_cuahang";

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
}
?>
