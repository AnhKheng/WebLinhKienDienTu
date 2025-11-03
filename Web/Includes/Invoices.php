
<?php

ob_clean();
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
class HoaDon {
    public $lastError = "";
    private $conn;
    private $table = "tbl_hoadonban";

    public function __construct($db) {
        $this->conn = $db;
    }

    // 📋 Lấy toàn bộ hóa đơn (join tên nhân viên, khách hàng, cửa hàng)
    public function getAll() {
        $sql = "SELECT hd.MaHD, hd.NgayBan, hd.MaNV,  
                       hd.MaKH,
                       hd.MaCH, 
                       hd.TongTien
                FROM tbl_hoadonban hd ";
                
                
        $result = $this->conn->query($sql);
        return $result;
    }

    // 🧾 Lấy chi tiết hóa đơn
public function getChiTiet($maHD) {
    $sql = "SELECT cthd.MaHD, cthd.MaSP, sp.TenSP, cthd.SoLuong, cthd.DonGia, 
                   (cthd.SoLuong * cthd.DonGia) AS ThanhTien
            FROM tbl_chitiethoadon cthd
            LEFT JOIN tbl_sanpham sp ON cthd.MaSP = sp.MaSP
            WHERE cthd.MaHD = ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("s", $maHD);
    $stmt->execute();
    $result = $stmt->get_result();

    $chiTietList = [];
    while ($row = $result->fetch_assoc()) {
        $chiTietList[] = $row;
    }
    return $chiTietList;
}


    public function getById($maHD) {
  $sql = "SELECT * FROM tbl_hoadonban WHERE MaHD = ?";
  $stmt = $this->conn->prepare($sql);
  $stmt->bind_param("s", $maHD);
  $stmt->execute();
  $result = $stmt->get_result();
  return $result->fetch_assoc();
}

//----------------------------update---------------
public function update($data) {
  // Bắt đầu transaction để đảm bảo an toàn dữ liệu
  $this->conn->begin_transaction();

  try {
    // 1️⃣ Cập nhật thông tin hóa đơn
    $sql = "UPDATE tbl_hoadonban 
            SET NgayBan=?, MaNV=?, MaKH=?, MaCH=?, TongTien=? 
            WHERE MaHD=?";
    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("sssdds",
      $data['NgayBan'],
      $data['MaNV'],
      $data['MaKH'],
      $data['MaCH'],
      $data['TongTien'],
      $data['MaHD']
    );

    if (!$stmt->execute()) {
      throw new Exception("Không thể cập nhật hóa đơn");
    }

    // 2️⃣ Nếu có mảng chi tiết thì cập nhật lại
    if (isset($data['ChiTiet']) && is_array($data['ChiTiet'])) {
      // Xóa chi tiết cũ
      $del = $this->conn->prepare("DELETE FROM tbl_chitiethoadon WHERE MaHD=?");
      $del->bind_param("s", $data['MaHD']);
      if (!$del->execute()) {
        throw new Exception("Không thể xóa chi tiết cũ");
      }

      // Thêm chi tiết mới
      $insert = $this->conn->prepare("INSERT INTO tbl_chitiethoadon (MaHD, MaSP, SoLuong, DonGia)
                                      VALUES (?, ?, ?, ?)");
      foreach ($data['ChiTiet'] as $ct) {
        $insert->bind_param("ssid",
          $data['MaHD'],
          $ct['MaSP'],
          $ct['SoLuong'],
          $ct['DonGia']
        );
        if (!$insert->execute()) {
          throw new Exception("Lỗi khi thêm chi tiết hóa đơn");
        }
      }
    }

    // 3️⃣ Commit nếu mọi thứ thành công
    $this->conn->commit();
    return true;

  } catch (Exception $e) {
    $this->conn->rollback();
    error_log("❌ Update hóa đơn lỗi: " . $e->getMessage());
    return false;
  }
}

//------------------------------delete--------------------------
public function delete($maHD) {
  // Bắt đầu transaction để đảm bảo tính toàn vẹn dữ liệu
    $this->conn->begin_transaction();

    try {
        
        $sqlChiTiet = "DELETE FROM tbl_chitiethoadon WHERE MaHD = ?";
        $stmtChiTiet = $this->conn->prepare($sqlChiTiet);
        $stmtChiTiet->bind_param("s", $maHD);
        $stmtChiTiet->execute();
       
        $sqlHD = "DELETE FROM tbl_hoadonban WHERE MaHD = ?";
        $stmtHD = $this->conn->prepare($sqlHD);
        $stmtHD->bind_param("s", $maHD);
        $stmtHD->execute();
       
        $this->conn->commit();
        return true;
    } catch (Exception $e) {
        
        $this->conn->rollback();
        error_log("Lỗi khi xóa hóa đơn: " . $e->getMessage());
        return false;
    }
}

// --------------------------thêm hóa đơn----------------------
// --------------------------thêm hóa đơn----------------------
public function add($data) {
    $this->conn->begin_transaction();

    try {
        // 1️⃣ Thêm hóa đơn mới
        $sql = "INSERT INTO tbl_hoadonban (MaHD, NgayBan, MaNV, MaKH, MaCH, TongTien)
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            "sssssd",
            $data['MaHD'],
            $data['NgayBan'],
            $data['MaNV'],
            $data['MaKH'],
            $data['MaCH'],
            $data['TongTien']
        );

        if (!$stmt->execute()) {
            throw new Exception("❌ Không thể thêm hóa đơn: " . $stmt->error);
        }

        // 2️⃣ Thêm chi tiết hóa đơn + trừ tồn kho
        if (!empty($data['ChiTiet']) && is_array($data['ChiTiet'])) {
            // Chuẩn bị câu lệnh thêm chi tiết
            $insert = $this->conn->prepare(
                "INSERT INTO tbl_chitiethoadon (MaHD, MaSP, SoLuong, DonGia)
                 VALUES (?, ?, ?, ?)"
            );

            // Chuẩn bị câu lệnh kiểm tra và cập nhật tồn kho
            $checkTon = $this->conn->prepare(
                "SELECT SoLuongTon FROM tbl_kho WHERE MaSP = ? AND MaCH = ?"
            );

            $updateTon = $this->conn->prepare(
                "UPDATE tbl_kho 
                 SET SoLuongTon = SoLuongTon - ? 
                 WHERE MaSP = ? AND MaCH = ? AND SoLuongTon >= ?"
            );

            if (!$insert || !$checkTon || !$updateTon) {
                throw new Exception("❌ Lỗi prepare SQL: " . $this->conn->error);
            }

            // Duyệt từng sản phẩm trong hóa đơn
            foreach ($data['ChiTiet'] as $ct) {
                $maSP = $ct['MaSP'];
                $soLuong = (int)$ct['SoLuong'];
                $donGia = (float)$ct['DonGia'];

                // 2.1️⃣ Kiểm tra tồn kho
                $checkTon->bind_param("ss", $maSP, $data['MaCH']);
                $checkTon->execute();
                $res = $checkTon->get_result();
                $row = $res->fetch_assoc();
                $ton = $row['SoLuongTon'] ?? 0;

                if ($ton < $soLuong) {
                    throw new Exception("⚠️ Sản phẩm $maSP không đủ tồn kho (còn $ton, cần $soLuong).");
                }

                // 2.2️⃣ Thêm chi tiết hóa đơn
                $insert->bind_param("ssid", $data['MaHD'], $maSP, $soLuong, $donGia);
                if (!$insert->execute()) {
                    throw new Exception("❌ Không thể thêm chi tiết hóa đơn: " . $insert->error);
                }

                // 2.3️⃣ Trừ tồn kho
                $updateTon->bind_param("issi", $soLuong, $maSP, $data['MaCH'], $soLuong);
                if (!$updateTon->execute() || $updateTon->affected_rows === 0) {
                    throw new Exception("❌ Không thể cập nhật tồn kho cho sản phẩm $maSP.");
                }
            }
        }

        // ✅ Commit nếu mọi thứ OK
        $this->conn->commit();
        return true;

    } catch (Exception $e) {
        // ❌ Rollback nếu có lỗi
        $this->conn->rollback();
        $this->lastError = $e->getMessage();
        error_log("❌ Add hóa đơn lỗi: " . $e->getMessage());
        return false;
    }
}

}
?>
