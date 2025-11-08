
<?php

ob_clean();
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
class HoaDonNhap {
    public $lastError = "";
    private $conn;
    private $table = "tbl_phieunhap";

    public function __construct($db) {
        $this->conn = $db;
    }

    // ================1 Lấy toàn bộ phieu nhap
    public function getAll() {
        $sql = "SELECT pn.MaPN, pn.NgayNhap, pn.MaNCC,  
                       pn.MaNV,
                       pn.MaCH, 
                       pn.TongTien
                FROM tbl_phieunhap pn ";
                
                
        $result = $this->conn->query($sql);
        return $result;
    }

    // ========== 2 Lấy chi tiết hóa đơn
public function getChiTiet($maPN) {
    $sql = "SELECT ctpn.MaPN, ctpn.MaSP, sp.TenSP, ctpn.SoLuong, ctpn.DonGiaNhap, 
                   (ctpn.SoLuong * ctpn.DonGiaNhap) AS ThanhTien
            FROM tbl_chitietphieunhap ctpn
            LEFT JOIN tbl_sanpham sp ON ctpn.MaSP = sp.MaSP
            WHERE ctpn.MaPN = ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("s", $maPN);
    $stmt->execute();
    $result = $stmt->get_result();

    $chiTietList = [];
    while ($row = $result->fetch_assoc()) {
        $chiTietList[] = $row;
    }
    return $chiTietList;
}


    // ==================3 Lấy phiếu nhập theo mã ==================
    public function getById($maPN) {
        $sql = "SELECT * FROM tbl_phieunhap WHERE MaPN = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $maPN);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    // ================== 3 Xóa phiếu nhập ==================
    public function delete($maPN) {
        $this->conn->begin_transaction();

        try {
            // Xóa chi tiết phiếu nhập
            $sqlCT = "DELETE FROM tbl_chitietphieunhap WHERE MaPN = ?";
            $stmtCT = $this->conn->prepare($sqlCT);
            $stmtCT->bind_param("s", $maPN);
            $stmtCT->execute();

            // Xóa phiếu nhập
            $sqlPN = "DELETE FROM tbl_phieunhap WHERE MaPN = ?";
            $stmtPN = $this->conn->prepare($sqlPN);
            $stmtPN->bind_param("s", $maPN);
            $stmtPN->execute();

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("Lỗi khi xóa phiếu nhập: " . $e->getMessage());
            return false;
        }
    }

    // ================== 4 Thêm phiếu nhập mới ==================
    public function add($data) {
        $this->conn->begin_transaction();

        try {
            // 1️⃣ Thêm phiếu nhập
            $sql = "INSERT INTO tbl_phieunhap (MaPN, NgayNhap, MaNCC, MaNV, MaCH, TongTien)
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param(
                "sssssd",
                $data['MaPN'],
                $data['NgayNhap'],
                $data['MaNCC'],
                $data['MaNV'],
                $data['MaCH'],
                $data['TongTien']
            );

            if (!$stmt->execute()) {
                throw new Exception("Không thể thêm phiếu nhập: " . $stmt->error);
            }

            // 2️⃣ Thêm chi tiết phiếu nhập + cập nhật tồn kho
            if (!empty($data['ChiTiet']) && is_array($data['ChiTiet'])) {
                // Chuẩn bị câu lệnh
                $insertCT = $this->conn->prepare("
                    INSERT INTO tbl_chitietphieunhap (MaPN, MaSP, SoLuong, DonGiaNhap)
                    VALUES (?, ?, ?, ?)
                ");

                // Kiểm tra tồn kho
                $checkTon = $this->conn->prepare("
                    SELECT SoLuongTon FROM tbl_kho WHERE MaSP = ? AND MaCH = ?
                ");

                // Cập nhật tồn kho (cộng thêm)
                $updateTon = $this->conn->prepare("
                    UPDATE tbl_kho 
                    SET SoLuongTon = SoLuongTon + ? 
                    WHERE MaSP = ? AND MaCH = ?
                ");

                if (!$insertCT || !$checkTon || !$updateTon) {
                    throw new Exception("Lỗi prepare SQL: " . $this->conn->error);
                }

                foreach ($data['ChiTiet'] as $ct) {
                    $maSP = $ct['MaSP'];
                    $soLuong = (int)$ct['SoLuong'];
                    $donGiaNhap = (float)$ct['DonGiaNhap'];

                    // 2.1️⃣ Kiểm tra sản phẩm đã tồn tại trong kho chưa
                    $checkTon->bind_param("ss", $maSP, $data['MaCH']);
                    $checkTon->execute();
                    $res = $checkTon->get_result();

                    if ($res->num_rows > 0) {
                        // 2.2️⃣ Cập nhật tồn kho
                        $updateTon->bind_param("iss", $soLuong, $maSP, $data['MaCH']);
                        if (!$updateTon->execute()) {
                            throw new Exception("Không thể cập nhật tồn kho cho sản phẩm $maSP.");
                        }
                    } else {
                        // 2.3️⃣ Nếu chưa có trong kho → thêm mới
                        $insertKho = $this->conn->prepare("
                            INSERT INTO tbl_kho (MaSP, MaCH, SoLuongTon)
                            VALUES (?, ?, ?)
                        ");
                        $insertKho->bind_param("ssi", $maSP, $data['MaCH'], $soLuong);
                        if (!$insertKho->execute()) {
                            throw new Exception("Không thể thêm mới sản phẩm vào kho: $maSP.");
                        }
                    }

                    // 2.4️⃣ Thêm chi tiết phiếu nhập
                    $insertCT->bind_param("ssid", $data['MaPN'], $maSP, $soLuong, $donGiaNhap);
                    if (!$insertCT->execute()) {
                        throw new Exception("Không thể thêm chi tiết phiếu nhập: " . $insertCT->error);
                    }
                }
            }

            // ✅ Commit nếu OK
            $this->conn->commit();
            return true;

        } catch (Exception $e) {
            // ❌ Rollback nếu có lỗi
            $this->conn->rollback();
            $this->lastError = $e->getMessage();
            error_log("Add phiếu nhập lỗi: " . $e->getMessage());
            return false;
        }
    }

}
?>
