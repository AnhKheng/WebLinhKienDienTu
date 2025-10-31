
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

    // 🧾 Thêm hóa đơn mới
    public function add($MaHD, $NgayBan, $MaNV, $MaKH, $MaCH, $TongTien) {
        $sql = "INSERT INTO $this->table (MaHD, NgayBan, MaNV, MaKH, MaCH, TongTien)
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sssssf", $MaHD, $NgayBan, $MaNV, $MaKH, $MaCH, $TongTien);
        return $stmt->execute();
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

public function update($data) {
  $sql = "UPDATE tbl_hoadonban SET NgayBan=?, MaNV=?, MaKH=?, MaCH=?, TongTien=? WHERE MaHD=?";
  $stmt = $this->conn->prepare($sql);
  $stmt->bind_param("sssdds", $data['NgayBan'], $data['MaNV'], $data['MaKH'], $data['MaCH'], $data['TongTien'], $data['MaHD']);
  return $stmt->execute();
}

public function delete($maHD) {
  // 1️⃣ Bắt đầu transaction để đảm bảo tính toàn vẹn dữ liệu
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

// public function add($data) {
//     try {
//         $sql = "INSERT INTO tbl_hoadonban (MaHD, NgayLap, MaNV, MaKH, MaCH, TongTien)
//                 VALUES (?, ?, ?, ?, ?, ?)";
//         $stmt = $this->conn->prepare($sql);
//         $stmt->bind_param(
//             "sssssd",
//             $data['MaHD'],
//             $data['NgayLap'],
//             $data['MaNV'],
//             $data['MaKH'],
//             $data['MaCH'],
//             $data['TongTien']
//         );

//         if ($stmt->execute()) {
//             return true;
//         } else {
//             error_log("Lỗi khi thêm hóa đơn: " . $stmt->error);
//             return false;
//         }
//     } catch (Exception $e) {
//         error_log("Lỗi khi thêm hóa đơn: " . $e->getMessage());
//         return false;
//     }
// }


}
?>
