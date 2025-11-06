<?php
include_once '../../Includes/Cart.php';


class GioHangBUS {
    private $dal;

    public function __construct($connect) {
        $this->dal = new GioHangDaL($connect);
    }

    public function layGioHang($maTKKH) {
        $cart = $this->dal->getGioHangActive($maTKKH);
        if ($cart && $cart->num_rows == 0) {
            $this->dal->taoGioHangMoi($maTKKH);
        }

        $result = $this->dal->getMaGH($maTKKH);
        if ($result && $row = $result->fetch_assoc()) {
            return $row['MaGH'];
        }
        return null;
    }

    public function layChiTiet($maGH) {
        return $this->dal->getChiTietGioHang($maGH);
    }

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

    // Thêm sản phẩm
    public function themSanPham($maTKKH, $maSP, $soLuong = 1) {
        $maGH = $this->layGioHang($maTKKH);
        if ($maGH) {
            return $this->dal->themSanPhamVaoGio($maGH, $maSP, $soLuong);
        }
        return false;
    }
    
    // Tăng sản phẩm
    public function tangSanPham($maTKKH, $maSP) {
        $maGH = $this->layGioHang($maTKKH);
        if ($maGH) {
            return $this->dal->tangSoLuong($maGH, $maSP);
        }
        return false;
    }

    // Giảm sản phẩm
    public function giamSanPham($maTKKH, $maSP) {
        $maGH = $this->layGioHang($maTKKH);
        if ($maGH) {
            return $this->dal->giamSoLuong($maGH, $maSP);
        }
        return false;
    }


    // Xóa sản phẩm
    public function xoaSanPham($maTKKH, $maSP) {
        $maGH = $this->layGioHang($maTKKH);
        if ($maGH) {
            return $this->dal->xoaSanPhamKhoiGio($maGH, $maSP);
        }
        return false;
    }
}
?>
