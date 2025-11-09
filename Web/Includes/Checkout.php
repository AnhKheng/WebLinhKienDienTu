<?php
class MuaHangDaL {
    private $connect;

    public function __construct($connect) {
        $this->connect = $connect;
    }

    public function getMaKHByMaTKKH($maTKKH) {
    $sql = "SELECT MaKH FROM tbl_taikhoankhachhang WHERE MaTKKH = '$maTKKH' LIMIT 1";
    $result = $this->connect->query($sql);
    if ($result && $row = $result->fetch_assoc()) {
        return $row['MaKH'];
    }
    return null;
}
    // ✅ 1. Lấy giỏ hàng đang active của tài khoản
    public function getGioHangActive($maTKKH) {
        $sql = "SELECT * FROM tbl_giohang WHERE MaTKKH = '$maTKKH' AND TrangThai = 'active'";
        return $this->connect->query($sql);
    }

    // ✅ 2. Lấy mã giỏ hàng hiện tại
    public function getMaGH($maTKKH) {
        $sql = "SELECT MaGH FROM tbl_giohang WHERE MaTKKH = '$maTKKH' AND TrangThai = 'active'";
        return $this->connect->query($sql);
    }

    // ✅ 3. Lấy chi tiết giỏ hàng (có cả giá và tên sản phẩm)
    public function getChiTietGioHang($maGH) {
        $sql = "SELECT ctgh.*, sp.TenSP, sp.DonGia AS GiaBan, sp.HinhAnh 
                FROM tbl_chitietgiohang AS ctgh 
                JOIN tbl_sanpham AS sp ON ctgh.MaSP = sp.MaSP 
                WHERE ctgh.MaGH = '$maGH'";
        return $this->connect->query($sql);
    }

    // ✅ 4. Kiểm tra cửa hàng còn đủ số lượng để bán
    public function getCuaHangCoHang($maSP, $soLuong) {
        $sql = "SELECT MaCH FROM tbl_kho 
                WHERE MaSP = '$maSP' AND SoLuongTon >= $soLuong 
                ORDER BY SoLuongTon DESC 
                LIMIT 1";
        $result = $this->connect->query($sql);
        if ($result && $row = $result->fetch_assoc()) {
            return $row['MaCH'];
        }
        return null; // Không đủ hàng
    }

    // ✅ 5. Sinh mã hóa đơn mới
    public function getNextMaHD() {
        // Lấy số lớn nhất hiện có trong cột MaHD (bỏ tiền tố HD)
        $sql = "SELECT MAX(CAST(SUBSTRING(MaHD, 3) AS UNSIGNED)) AS MaxMaHD FROM tbl_hoadonban";
        $result = $this->connect->query($sql);

        if (!$result) {
            // Truy vấn lỗi — trả về mã mặc định hoặc xử lý lỗi theo nhu cầu
            // Có thể ghi log $this->connect->error
            return 'HD001';
        }

        $row = $result->fetch_assoc();
        $maxNum = null;
        if ($row && isset($row['MaxMaHD'])) {
            $maxNum = intval($row['MaxMaHD']);
        }

        $nextNumber = ($maxNum !== null && $maxNum > 0) ? ($maxNum + 1) : 1;
        return 'HD' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    // ✅ 6. Thêm hóa đơn mới
    public function themHoaDon($maHD, $maKH, $maCH, $tongTien) {
        $ngayBan = date('Y-m-d H:i:s');
        $sql = "INSERT INTO tbl_hoadonban (MaHD, NgayBan, MaKH, MaCH, TongTien)
                VALUES ('$maHD', '$ngayBan', '$maKH', '$maCH', $tongTien)";
        $result = $this->connect->query($sql);
        if (!$result) {
            die("Lỗi SQL khi thêm hóa đơn: " . $this->connect->error . "<br>Truy vấn: " . $sql);
        }
        return $result;
    }

    // ✅ 7. Thêm chi tiết hóa đơn
    public function themChiTietHoaDon($maHD, $maSP, $soLuong, $donGia) {
        $sql = "INSERT INTO tbl_chitiethoadon (MaHD, MaSP, SoLuong, DonGia)
                VALUES ('$maHD', '$maSP', $soLuong, $donGia)";
        return $this->connect->query($sql);
    }

    // ✅ 8. Trừ sản phẩm trong kho sau khi mua
    public function truSanPhamKho($maCH, $maSP, $soLuong) {
        $sql = "UPDATE tbl_kho 
                SET SoLuongTon = SoLuongTon - $soLuong 
                WHERE MaCH = '$maCH' AND MaSP = '$maSP'";
        return $this->connect->query($sql);
    }

    // ✅ 9. Cập nhật trạng thái giỏ hàng sau khi thanh toán
    public function capNhatTrangThaiGioHang($maGH) {
        $sql = "UPDATE tbl_giohang SET TrangThai = 'checked_out' WHERE MaGH = '$maGH'";
        return $this->connect->query($sql);
    }
}
?>
