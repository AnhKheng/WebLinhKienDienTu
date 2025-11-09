<?php
include_once '../../Includes/Checkout.php'; // Giả sử file này đã include DAL

class MuaHangBUS {
    private $dal;

    public function __construct($connect) {
        $this->dal = new MuaHangDaL($connect);
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

    // ✅ 6. Thêm hóa đơn mới
    public function themHoaDon($maKH, $maCH, $tongTien) {
        return $this->dal->themHoaDon($maHD, $maKH, $maCH, $tongTien) ? $maHD : false;
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

    // ✅ 10. Quy trình mua hàng hoàn chỉnh
    public function xuLyMuaHang($maTKKH, $maCH)
    {
        // 1. Lấy maGH
        $maGH = $this->layGioHang($maTKKH);
        if (!$maGH) return "Không tìm thấy giỏ hàng.";

        // 2. Lấy chi tiết
        $chiTiet = $this->layChiTiet($maGH);
        if (!$chiTiet || $chiTiet->num_rows == 0) return "Giỏ hàng trống.";

        // 3. Tính tổng tiền
        $tongTien = $this->tinhTongTien($maGH);

        // 4. Lấy MaKH thực (mapping từ MaTKKH -> MaKH)
        $maKH = $this->dal->getMaKHByMaTKKH($maTKKH);
        if (!$maKH) return "Không tìm thấy thông tin khách hàng.";

        // 5. Sinh mã hóa đơn **CHỈ 1 LẦN**
        $maHD = $this->taoMaHoaDonMoi(); // gọi đến DAL->getNextMaHD()

        // 6. Thêm hóa đơn (nếu thêm thất bại, trả lỗi)
        $themHD = $this->dal->themHoaDon($maHD, $maKH, $maCH, $tongTien);
        if (!$themHD) {
            return "Lỗi khi tạo hóa đơn! " . $this->dal->getLastErrorMessage(); // tùy bạn implement
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

        return "Thanh toán thành công! Mã hóa đơn: $maHD";
    }

}
?>
