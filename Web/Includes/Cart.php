<?php
class GioHangDaL {
    private $connect;

    public function __construct($connect) {
        $this->connect = $connect;
    }

    // Lấy giỏ hàng đang active của tài khoản
    public function getGioHangActive($maTKKH) {
        $sql = "SELECT * FROM tbl_giohang WHERE MaTKKH = '$maTKKH' AND TrangThai = 'active'";
        return $this->connect->query($sql);
    }

    // Tạo giỏ hàng mới
    public function taoGioHangMoi($maTKKH) {
        $sql = "INSERT INTO tbl_giohang(MaTKKH) VALUES('$maTKKH')";
        return $this->connect->query($sql);
    }

    // Lấy mã giỏ hàng hiện tại
    public function getMaGH($maTKKH) {
        $sql = "SELECT MaGH FROM tbl_giohang WHERE MaTKKH = '$maTKKH' AND TrangThai = 'active'";
        return $this->connect->query($sql);
    }

    // Lấy chi tiết giỏ hàng
    public function getChiTietGioHang($maGH) {
        $sql = "SELECT ctgh.*, sp.TenSP, sp.DonGia, sp.HinhAnh 
                FROM tbl_chitietgiohang AS ctgh 
                JOIN tbl_sanpham AS sp ON ctgh.MaSP = sp.MaSP 
                WHERE ctgh.MaGH = '$maGH'";
        return $this->connect->query($sql);
    }

    // Thêm sản phẩm vào chi tiết giỏ hàng
    public function themSanPhamVaoGio($maGH, $maSP, $soLuong) {
        // Nếu sản phẩm đã có trong giỏ thì tăng số lượng
        $sql_check = "SELECT * FROM tbl_chitietgiohang WHERE MaGH = '$maGH' AND MaSP = '$maSP'";
        $result = $this->connect->query($sql_check);
        if ($result && $result->num_rows > 0) {
            $sql_update = "UPDATE tbl_chitietgiohang 
                           SET SoLuong = SoLuong + $soLuong 
                           WHERE MaGH = '$maGH' AND MaSP = '$maSP'";
            return $this->connect->query($sql_update);
        } else {
            $sql_insert = "INSERT INTO tbl_chitietgiohang(MaGH, MaSP, SoLuong) 
                           VALUES('$maGH', '$maSP', $soLuong)";
            return $this->connect->query($sql_insert);
        }
    }

    // Tăng số lượng sản phẩm trong giỏ
    public function tangSoLuong($maGH, $maSP) {
        $sql = "UPDATE tbl_chitietgiohang 
                SET SoLuong = SoLuong + 1 
                WHERE MaGH = '$maGH' AND MaSP = '$maSP'";
        return $this->connect->query($sql);
    }

    // Giảm số lượng sản phẩm (nếu còn > 1 thì trừ đi 1, nếu =1 thì xóa luôn)
    public function giamSoLuong($maGH, $maSP) {
        $sql_check = "SELECT SoLuong FROM tbl_chitietgiohang WHERE MaGH = '$maGH' AND MaSP = '$maSP'";
        $result = $this->connect->query($sql_check);
        if ($result && $row = $result->fetch_assoc()) {
            if ($row['SoLuong'] > 1) {
                $sql_update = "UPDATE tbl_chitietgiohang 
                            SET SoLuong = SoLuong - 1 
                            WHERE MaGH = '$maGH' AND MaSP = '$maSP'";
                return $this->connect->query($sql_update);
            } else {
                // Xóa nếu chỉ còn 1
                $sql_delete = "DELETE FROM tbl_chitietgiohang WHERE MaGH = '$maGH' AND MaSP = '$maSP'";
                return $this->connect->query($sql_delete);
            }
        }
        return false;
    }


    // Xóa sản phẩm khỏi giỏ
    public function xoaSanPhamKhoiGio($maGH, $maSP) {
        $sql = "DELETE FROM tbl_chitietgiohang WHERE MaGH = '$maGH' AND MaSP = '$maSP'";
        return $this->connect->query($sql);
    }
}
?>