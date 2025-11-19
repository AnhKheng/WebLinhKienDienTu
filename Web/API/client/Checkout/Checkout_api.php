<?php
include_once '../../Includes/Checkout.php'; // Giả sử file này đã include DAL

class MuaHangBUS {
    private $dal;
    private $conn; // Biến lưu kết nối database

    public function __construct($connect) {
        $this->dal = new MuaHangDaL($connect);
        $this->conn = $connect; // Lưu kết nối vào biến của class này để dùng
    }

    // ✅ 1. Lấy mã giỏ hàng đang active của tài khoản
    public function layGioHang($maTKKH) {
        $result = $this->dal->getMaGH($maTKKH);
        if ($result && $row = $result->fetch_assoc()) {
            return $row['MaGH'];
        }
        return null;
    }

    // ✅ 2. Lấy chi tiết sản phẩm trong giỏ hàng
    public function layChiTiet($maGH) {
        return $this->dal->getChiTietGioHang($maGH);
    }

    // ✅ 3. Tính tổng tiền giỏ hàng
    public function tinhTongTien($maGH) {
        $tong = 0;
        $ds = $this->layChiTiet($maGH);
        if ($ds) {
            while ($row = $ds->fetch_assoc()) {
                $tong += $row['GiaBan'] * $row['SoLuong'];
            }
        }
        return $tong;
    }

    // ✅ 4. Kiểm tra cửa hàng có đủ hàng
    public function kiemTraSanPhamTon($maSP, $soLuong) {
        $maCH = $this->dal->getCuaHangCoHang($maSP, $soLuong);
        if ($maCH !== null) {
            return $maCH; // có cửa hàng đủ hàng
        }
        return false; // không đủ hàng
    }

    // ✅ 5. Tạo mã hóa đơn mới
    public function taoMaHoaDonMoi() {
        return $this->dal->getNextMaHD();
    }

    // ✅ 6. Thêm hóa đơn mới (Hàm này được viết đè để hỗ trợ Địa chỉ)
    public function themHoaDonMoi($maHD, $maKH, $maCH, $tongTien, $diaChi) {
        $ngayBan = date('Y-m-d H:i:s');
        
        // Sử dụng $this->conn đã lưu thay vì truy cập vào DAL
        $sql = "INSERT INTO tbl_hoadonban (MaHD, NgayBan, MaKH, DiaChi, MaCH, TongTien) 
                VALUES ('$maHD', '$ngayBan', '$maKH', '$diaChi', '$maCH', '$tongTien')";
        
        return mysqli_query($this->conn, $sql);
    }

    // ✅ 7. Thêm chi tiết hóa đơn
    public function themChiTietHoaDon($maHD, $maSP, $soLuong, $donGia) {
        return $this->dal->themChiTietHoaDon($maHD, $maSP, $soLuong, $donGia);
    }

    // ✅ 8. Giảm sản phẩm tồn trong kho
    public function giamSanPham($maCH, $maSP, $soLuong) {
        return $this->dal->truSanPhamKho($maCH, $maSP, $soLuong);
    }

    // ✅ 9. Cập nhật giỏ hàng đã thanh toán
    public function capNhatGioHang($maGH) {
        return $this->dal->capNhatTrangThaiGioHang($maGH);
    }

    // ✅ 10. Quy trình mua hàng hoàn chỉnh (Đã cập nhật nhận Địa chỉ)
    public function xuLyMuaHang($maTKKH, $maCH, $diaChi)
    {
        // 1. Lấy maGH
        $maGH = $this->layGioHang($maTKKH);
        if (!$maGH) return ['success' => false, 'message' => 'Không tìm thấy giỏ hàng.'];

        // 2. Lấy chi tiết
        $chiTiet = $this->layChiTiet($maGH);
        if (!$chiTiet || $chiTiet->num_rows == 0) return ['success' => false, 'message' => 'Giỏ hàng trống.'];

        // 3. Tính tổng tiền
        $tongTien = $this->tinhTongTien($maGH);

        // 4. Lấy MaKH thực (mapping từ MaTKKH -> MaKH)
        $maKH = $this->dal->getMaKHByMaTKKH($maTKKH);
        if (!$maKH) return ['success' => false, 'message' => 'Không tìm thấy thông tin khách hàng.'];

        // 5. Sinh mã hóa đơn **CHỈ 1 LẦN**
        $maHD = $this->taoMaHoaDonMoi(); 

        // 6. Thêm hóa đơn (GỌI HÀM MỚI CÓ ĐỊA CHỈ)
        $themHD = $this->themHoaDonMoi($maHD, $maKH, $maCH, $tongTien, $diaChi);
        
        if (!$themHD) {
            // Lấy lỗi từ connection
            $errorMsg = mysqli_error($this->conn) ?? 'Lỗi khi tạo hóa đơn!';
            return ['success' => false, 'message' => $errorMsg];
        }

        // 7. Thêm chi tiết và trừ kho
        // Reset pointer nếu cần
        $chiTiet->data_seek(0);
        while ($row = $chiTiet->fetch_assoc()) {
            $this->themChiTietHoaDon($maHD, $row['MaSP'], $row['SoLuong'], $row['GiaBan']);
            $this->giamSanPham($maCH, $row['MaSP'], $row['SoLuong']);
        }

        // 8. Cập nhật giỏ hàng
        $this->capNhatGioHang($maGH);

        // 9. TRẢ VỀ MẢNG THÀNH CÔNG
        return ['success' => true, 'MaHD' => $maHD];
    }

}
?>