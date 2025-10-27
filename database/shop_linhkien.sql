

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `shop_linhkien`
--
DROP DATABASE IF EXISTS `shop_linhkien`;
CREATE DATABASE IF NOT EXISTS `shop_linhkien` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
USE `shop_linhkien`;

-- Tạo các bảng
CREATE TABLE IF NOT EXISTS `tbl_danhmuc` (
  `MaDM` varchar(10) CHARACTER SET utf8 NOT NULL,
  `TenDM` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`MaDM`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `tbl_sanpham` (
  `MaSP` varchar(10) CHARACTER SET utf8 NOT NULL,
  `TenSP` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  `MaDM` varchar(10) CHARACTER SET utf8 DEFAULT NULL,
  `DonGia` float DEFAULT NULL,
  `MoTa` varchar(500) CHARACTER SET utf8 DEFAULT NULL,
  `TrangThai` bit(1) DEFAULT b'1',
  `HinhAnh` VARCHAR(255),
  PRIMARY KEY (`MaSP`),
  KEY `MaDM` (`MaDM`),
  CONSTRAINT `tbl_sanpham_ibfk_1` FOREIGN KEY (`MaDM`) REFERENCES `tbl_danhmuc` (`MaDM`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `tbl_nhacungcap` (
  `MaNCC` varchar(10) CHARACTER SET utf8 NOT NULL,
  `TenNCC` varchar(150) CHARACTER SET utf8 DEFAULT NULL,
  `DiaChi` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  `SoDienThoai` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`MaNCC`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `tbl_cuahang` (
  `MaCH` varchar(10) CHARACTER SET utf8 NOT NULL,
  `TenCH` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `DiaChi` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  `SoDienThoai` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`MaCH`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `tbl_nhanvien` (
  `MaNV` varchar(10) CHARACTER SET utf8 NOT NULL,
  `TenNV` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `GioiTinh` varchar(10) CHARACTER SET utf8 DEFAULT NULL,
  `NgaySinh` date DEFAULT NULL,
  `SoDienThoai` varchar(15) DEFAULT NULL,
  `MaCH` varchar(10) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`MaNV`),
  KEY `MaCH` (`MaCH`),
  CONSTRAINT `tbl_nhanvien_ibfk_1` FOREIGN KEY (`MaCH`) REFERENCES `tbl_cuahang` (`MaCH`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `tbl_taikhoan` (
  `TenDangNhap` varchar(50) NOT NULL,
  `MatKhau` varchar(255) NOT NULL,
  `MaNV` varchar(10) CHARACTER SET utf8 DEFAULT NULL,
  `VaiTro` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`TenDangNhap`),
  KEY `MaNV` (`MaNV`),
  CONSTRAINT `tbl_taikhoan_ibfk_1` FOREIGN KEY (`MaNV`) REFERENCES `tbl_nhanvien` (`MaNV`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `tbl_khachhang` (
  `MaKH` varchar(10) CHARACTER SET utf8 NOT NULL,
  `TenKH` varchar(150) CHARACTER SET utf8 DEFAULT NULL,
  `SoDienThoai` varchar(15) DEFAULT NULL,
  `DiaChi` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`MaKH`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `tbl_kho` (
  `MaCH` varchar(10) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `MaSP` varchar(10) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `SoLuongTon` int(11) DEFAULT '0',
  PRIMARY KEY (`MaCH`,`MaSP`),
  KEY `MaSP` (`MaSP`),
  CONSTRAINT `tbl_kho_ibfk_1` FOREIGN KEY (`MaCH`) REFERENCES `tbl_cuahang` (`MaCH`),
  CONSTRAINT `tbl_kho_ibfk_2` FOREIGN KEY (`MaSP`) REFERENCES `tbl_sanpham` (`MaSP`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `tbl_phieunhap` (
  `MaPN` varchar(10) CHARACTER SET utf8 NOT NULL,
  `NgayNhap` datetime DEFAULT NULL,
  `MaNCC` varchar(10) CHARACTER SET utf8 DEFAULT NULL,
  `MaNV` varchar(10) CHARACTER SET utf8 DEFAULT NULL,
  `MaCH` varchar(10) CHARACTER SET utf8 DEFAULT NULL,
  `TongTien` float DEFAULT NULL,
  PRIMARY KEY (`MaPN`),
  KEY `MaNCC` (`MaNCC`),
  KEY `MaNV` (`MaNV`),
  KEY `MaCH` (`MaCH`),
  CONSTRAINT `tbl_phieunhap_ibfk_1` FOREIGN KEY (`MaNCC`) REFERENCES `tbl_nhacungcap` (`MaNCC`),
  CONSTRAINT `tbl_phieunhap_ibfk_2` FOREIGN KEY (`MaNV`) REFERENCES `tbl_nhanvien` (`MaNV`),
  CONSTRAINT `tbl_phieunhap_ibfk_3` FOREIGN KEY (`MaCH`) REFERENCES `tbl_cuahang` (`MaCH`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `tbl_chitietphieunhap` (
  `MaPN` varchar(10) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `MaSP` varchar(10) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `SoLuong` int(11) DEFAULT NULL,
  `DonGiaNhap` float DEFAULT NULL,
  PRIMARY KEY (`MaPN`,`MaSP`),
  KEY `MaSP` (`MaSP`),
  CONSTRAINT `tbl_chitietphieunhap_ibfk_1` FOREIGN KEY (`MaPN`) REFERENCES `tbl_phieunhap` (`MaPN`),
  CONSTRAINT `tbl_chitietphieunhap_ibfk_2` FOREIGN KEY (`MaSP`) REFERENCES `tbl_sanpham` (`MaSP`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `tbl_hoadonban` (
  `MaHD` varchar(10) CHARACTER SET utf8 NOT NULL,
  `NgayBan` datetime DEFAULT NULL,
  `MaNV` varchar(10) CHARACTER SET utf8 DEFAULT NULL,
  `MaKH` varchar(10) CHARACTER SET utf8 DEFAULT NULL,
  `MaCH` varchar(10) CHARACTER SET utf8 DEFAULT NULL,
  `TongTien` float DEFAULT NULL,
  PRIMARY KEY (`MaHD`),
  KEY `MaNV` (`MaNV`),
  KEY `MaKH` (`MaKH`),
  KEY `MaCH` (`MaCH`),
  CONSTRAINT `tbl_hoadonban_ibfk_1` FOREIGN KEY (`MaNV`) REFERENCES `tbl_nhanvien` (`MaNV`),
  CONSTRAINT `tbl_hoadonban_ibfk_2` FOREIGN KEY (`MaKH`) REFERENCES `tbl_khachhang` (`MaKH`),
  CONSTRAINT `tbl_hoadonban_ibfk_3` FOREIGN KEY (`MaCH`) REFERENCES `tbl_cuahang` (`MaCH`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `tbl_chitiethoadon` (
  `MaHD` varchar(10) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `MaSP` varchar(10) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `SoLuong` int(11) DEFAULT NULL,
  `DonGia` float DEFAULT NULL,
  PRIMARY KEY (`MaHD`,`MaSP`),
  KEY `MaSP` (`MaSP`),
  CONSTRAINT `tbl_chitiethoadon_ibfk_1` FOREIGN KEY (`MaHD`) REFERENCES `tbl_hoadonban` (`MaHD`),
  CONSTRAINT `tbl_chitiethoadon_ibfk_2` FOREIGN KEY (`MaSP`) REFERENCES `tbl_sanpham` (`MaSP`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;