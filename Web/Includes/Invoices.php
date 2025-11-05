
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
  $this->conn->begin_transaction();

  try {
    // ---------- LOG incoming data (useful for debug) ----------
    error_log("DEBUG update payload: " . json_encode($data));

    // 0) Kiểm tra MaHD có hợp lệ
    if (empty($data['MaHD'])) {
      throw new Exception("MaHD không được để trống.");
    }

    $maHD = $data['MaHD'];

    // 1) Nếu MaCH không được gửi hoặc rỗng, lấy lại MaCH hiện tại của hóa đơn
    if (empty($data['MaCH'])) {
      $stmtOld = $this->conn->prepare("SELECT MaCH FROM tbl_hoadonban WHERE MaHD = ?");
      if (!$stmtOld) throw new Exception("Prepare lỗi (stmtOld): " . $this->conn->error);
      $stmtOld->bind_param("s", $maHD);
      $stmtOld->execute();
      $resOld = $stmtOld->get_result();
      if ($rowOld = $resOld->fetch_assoc()) {
        $data['MaCH'] = $rowOld['MaCH'];
      } else {
        throw new Exception("Không tìm thấy hóa đơn với MaHD = $maHD");
      }
    }

    // 2) Kiểm tra MaCH tồn tại trong tbl_cuahang
    $checkStore = $this->conn->prepare("SELECT MaCH FROM tbl_cuahang WHERE MaCH = ?");
    if (!$checkStore) throw new Exception("Prepare lỗi (checkStore): " . $this->conn->error);
    $checkStore->bind_param("s", $data['MaCH']);
    $checkStore->execute();
    $resStore = $checkStore->get_result();
    if ($resStore->num_rows == 0) {
      throw new Exception("Mã cửa hàng không tồn tại: " . $data['MaCH']);
    }

    // 3) (Tùy chọn) Kiểm tra MaNV và MaKH nếu bạn muốn đảm bảo tồn tại
    if (!empty($data['MaNV'])) {
      $checkNV = $this->conn->prepare("SELECT MaNV FROM tbl_nhanvien WHERE MaNV = ?");
      $checkNV->bind_param("s", $data['MaNV']);
      $checkNV->execute();
      if ($checkNV->get_result()->num_rows == 0) {
        throw new Exception("Mã nhân viên không tồn tại: " . $data['MaNV']);
      }
    }

    if (!empty($data['MaKH'])) {
      $checkKH = $this->conn->prepare("SELECT MaKH FROM tbl_khachhang WHERE MaKH = ?");
      $checkKH->bind_param("s", $data['MaKH']);
      $checkKH->execute();
      if ($checkKH->get_result()->num_rows == 0) {
        throw new Exception("Mã khách hàng không tồn tại: " . $data['MaKH']);
      }
    }

    // 4) Thực hiện UPDATE — **khai báo kiểu bind_param chính xác**
    // Nếu bạn KHÔNG muốn cho phép thay đổi MaCH, bạn có thể bỏ MaCH ra khỏi câu lệnh (như comment ở dưới)
    $sql = "UPDATE tbl_hoadonban 
            SET NgayBan = ?, MaNV = ?, MaKH = ?, MaCH = ?, TongTien = ? 
            WHERE MaHD = ?";
    $stmt = $this->conn->prepare($sql);
    if (!$stmt) throw new Exception("Prepare lỗi (update): " . $this->conn->error);

    // CHÚ Ý: kiểu phải là "ssssds" => s,s,s,s,d,s
    $ngayBan = $data['NgayBan'] ?? null;
    $maNV = $data['MaNV'] ?? null;
    $maKH = $data['MaKH'] ?? null;
    $maCH = $data['MaCH'] ?? null;
    $tongTien = isset($data['TongTien']) ? (float)$data['TongTien'] : 0.0;

    $stmt->bind_param("ssssds",
      $ngayBan,
      $maNV,
      $maKH,
      $maCH,
      $tongTien,
      $maHD
    );

    if (!$stmt->execute()) {
      throw new Exception("Không thể cập nhật hóa đơn: " . $stmt->error);
    }

    // 5) Cập nhật chi tiết nếu có — giống logic bạn đang dùng (xóa rồi insert)
    if (isset($data['ChiTiet']) && is_array($data['ChiTiet'])) {
      $del = $this->conn->prepare("DELETE FROM tbl_chitiethoadon WHERE MaHD = ?");
      if (!$del) throw new Exception("Prepare lỗi (del): " . $this->conn->error);
      $del->bind_param("s", $maHD);
      if (!$del->execute()) {
        throw new Exception("Không thể xóa chi tiết cũ: " . $del->error);
      }

      $insert = $this->conn->prepare("INSERT INTO tbl_chitiethoadon (MaHD, MaSP, SoLuong, DonGia) VALUES (?, ?, ?, ?)");
      if (!$insert) throw new Exception("Prepare lỗi (insert ct): " . $this->conn->error);

      foreach ($data['ChiTiet'] as $ct) {
        $maSP = $ct['MaSP'];
        $soLuong = (int)$ct['SoLuong'];
        $donGia = (float)$ct['DonGia'];

        $insert->bind_param("ssid", $maHD, $maSP, $soLuong, $donGia);
        if (!$insert->execute()) {
          throw new Exception("Lỗi thêm chi tiết: " . $insert->error);
        }
      }
    }

    $this->conn->commit();
    return true;

  } catch (Exception $e) {
    $this->conn->rollback();
    $this->lastError = $e->getMessage();
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
