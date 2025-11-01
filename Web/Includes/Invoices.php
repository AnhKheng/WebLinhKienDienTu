
<?php
ob_clean();
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
class HoaDon {
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
                // -- LEFT JOIN tbl_nhanvien nv ON hd.MaNV = nv.MaNV
                // -- LEFT JOIN tbl_khachhang kh ON hd.MaKH = kh.MaKH
                // -- LEFT JOIN tbl_cuahang ch ON hd.MaCH = ch.MaCH
                // -- ORDER BY hd.NgayBan DESC";
                
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

// --------------------------them hóa đon----------------------
public function add($data) {
    $this->conn->begin_transaction();

    try {
        // 1️⃣ Thêm hóa đơn
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
            throw new Exception("Không thể thêm hóa đơn: " . $stmt->error);
        }

        // 2️⃣ Thêm chi tiết hóa đơn (nếu có)
if (!empty($data['ChiTiet']) && is_array($data['ChiTiet'])) {
    $insert = $this->conn->prepare(
        "INSERT INTO tbl_chitiethoadon (MaHD, MaSP, SoLuong, DonGia)
         VALUES (?, ?, ?, ?)"
    );

    // ⚠️ Thêm đoạn kiểm tra lỗi prepare ở đây
    if (!$insert) {
        die("❌ Lỗi SQL (prepare chi tiết): " . $this->conn->error);
    }

    foreach ($data['ChiTiet'] as $ct) {
        $maSP = $ct['MaSP'];
        $soLuong = (int)$ct['SoLuong'];
        $donGia = (float)$ct['DonGia'];
        
        
        $insert->bind_param("ssid", $data['MaHD'], $maSP, $soLuong, $donGia);

        if (!$insert->execute()) {
            throw new Exception("Không thể thêm chi tiết hóa đơn: " . $insert->error);
        }
    }
}


        $this->conn->commit();
        return true;

    } catch (Exception $e) {
        $this->conn->rollback();
        error_log("❌ Add hóa đơn lỗi: " . $e->getMessage());
        return false;
    }
}




}
?>
