-- phpMyAdmin SQL Dump
-- version 3.4.9
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Oct 24, 2025 at 03:18 AM
-- Server version: 5.5.20
-- PHP Version: 5.3.10

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `shop_linhkien`
--

-- --------------------------------------------------------

--
-- Table structure for table `chitiethoadon`
--

CREATE TABLE IF NOT EXISTS `chitiethoadon` (
  `MaHD` varchar(10) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `MaSP` varchar(10) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `SoLuong` int(11) DEFAULT NULL,
  `DonGia` float DEFAULT NULL,
  PRIMARY KEY (`MaHD`,`MaSP`),
  KEY `MaSP` (`MaSP`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `chitietphieunhap`
--

CREATE TABLE IF NOT EXISTS `chitietphieunhap` (
  `MaPN` varchar(10) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `MaSP` varchar(10) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `SoLuong` int(11) DEFAULT NULL,
  `DonGiaNhap` float DEFAULT NULL,
  PRIMARY KEY (`MaPN`,`MaSP`),
  KEY `MaSP` (`MaSP`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `cuahang`
--

CREATE TABLE IF NOT EXISTS `cuahang` (
  `MaCH` varchar(10) CHARACTER SET utf8 NOT NULL,
  `TenCH` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `DiaChi` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  `SoDienThoai` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`MaCH`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `danhmuc`
--

CREATE TABLE IF NOT EXISTS `danhmuc` (
  `MaDM` varchar(10) CHARACTER SET utf8 NOT NULL,
  `TenDM` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`MaDM`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `hoadonban`
--

CREATE TABLE IF NOT EXISTS `hoadonban` (
  `MaHD` varchar(10) CHARACTER SET utf8 NOT NULL,
  `NgayBan` datetime DEFAULT NULL,
  `MaNV` varchar(10) CHARACTER SET utf8 DEFAULT NULL,
  `MaKH` varchar(10) CHARACTER SET utf8 DEFAULT NULL,
  `MaCH` varchar(10) CHARACTER SET utf8 DEFAULT NULL,
  `TongTien` float DEFAULT NULL,
  PRIMARY KEY (`MaHD`),
  KEY `MaNV` (`MaNV`),
  KEY `MaKH` (`MaKH`),
  KEY `MaCH` (`MaCH`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `khachhang`
--

CREATE TABLE IF NOT EXISTS `khachhang` (
  `MaKH` varchar(10) CHARACTER SET utf8 NOT NULL,
  `TenKH` varchar(150) CHARACTER SET utf8 DEFAULT NULL,
  `SoDienThoai` varchar(15) DEFAULT NULL,
  `DiaChi` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`MaKH`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `kho`
--

CREATE TABLE IF NOT EXISTS `kho` (
  `MaCH` varchar(10) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `MaSP` varchar(10) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `SoLuongTon` int(11) DEFAULT '0',
  PRIMARY KEY (`MaCH`,`MaSP`),
  KEY `MaSP` (`MaSP`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `nhacungcap`
--

CREATE TABLE IF NOT EXISTS `nhacungcap` (
  `MaNCC` varchar(10) CHARACTER SET utf8 NOT NULL,
  `TenNCC` varchar(150) CHARACTER SET utf8 DEFAULT NULL,
  `DiaChi` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  `SoDienThoai` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`MaNCC`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `nhanvien`
--

CREATE TABLE IF NOT EXISTS `nhanvien` (
  `MaNV` varchar(10) CHARACTER SET utf8 NOT NULL,
  `TenNV` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `GioiTinh` varchar(10) CHARACTER SET utf8 DEFAULT NULL,
  `NgaySinh` date DEFAULT NULL,
  `SoDienThoai` varchar(15) DEFAULT NULL,
  `MaCH` varchar(10) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`MaNV`),
  KEY `MaCH` (`MaCH`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `phieunhap`
--

CREATE TABLE IF NOT EXISTS `phieunhap` (
  `MaPN` varchar(10) CHARACTER SET utf8 NOT NULL,
  `NgayNhap` datetime DEFAULT NULL,
  `MaNCC` varchar(10) CHARACTER SET utf8 DEFAULT NULL,
  `MaNV` varchar(10) CHARACTER SET utf8 DEFAULT NULL,
  `MaCH` varchar(10) CHARACTER SET utf8 DEFAULT NULL,
  `TongTien` float DEFAULT NULL,
  PRIMARY KEY (`MaPN`),
  KEY `MaNCC` (`MaNCC`),
  KEY `MaNV` (`MaNV`),
  KEY `MaCH` (`MaCH`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sanpham`
--

CREATE TABLE IF NOT EXISTS `sanpham` (
  `MaSP` varchar(10) CHARACTER SET utf8 NOT NULL,
  `TenSP` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  `MaDM` varchar(10) CHARACTER SET utf8 DEFAULT NULL,
  `DonGia` float DEFAULT NULL,
  `MoTa` varchar(500) CHARACTER SET utf8 DEFAULT NULL,
  `TrangThai` bit(1) DEFAULT b'1',
  PRIMARY KEY (`MaSP`),
  KEY `MaDM` (`MaDM`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `taikhoan`
--

CREATE TABLE IF NOT EXISTS `taikhoan` (
  `TenDangNhap` varchar(50) NOT NULL,
  `MatKhau` varchar(255) NOT NULL,
  `MaNV` varchar(10) CHARACTER SET utf8 DEFAULT NULL,
  `VaiTro` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`TenDangNhap`),
  KEY `MaNV` (`MaNV`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `chitiethoadon`
--
ALTER TABLE `chitiethoadon`
  ADD CONSTRAINT `chitiethoadon_ibfk_1` FOREIGN KEY (`MaHD`) REFERENCES `hoadonban` (`MaHD`),
  ADD CONSTRAINT `chitiethoadon_ibfk_2` FOREIGN KEY (`MaSP`) REFERENCES `sanpham` (`MaSP`);

--
-- Constraints for table `chitietphieunhap`
--
ALTER TABLE `chitietphieunhap`
  ADD CONSTRAINT `chitietphieunhap_ibfk_1` FOREIGN KEY (`MaPN`) REFERENCES `phieunhap` (`MaPN`),
  ADD CONSTRAINT `chitietphieunhap_ibfk_2` FOREIGN KEY (`MaSP`) REFERENCES `sanpham` (`MaSP`);

--
-- Constraints for table `hoadonban`
--
ALTER TABLE `hoadonban`
  ADD CONSTRAINT `hoadonban_ibfk_1` FOREIGN KEY (`MaNV`) REFERENCES `nhanvien` (`MaNV`),
  ADD CONSTRAINT `hoadonban_ibfk_2` FOREIGN KEY (`MaKH`) REFERENCES `khachhang` (`MaKH`),
  ADD CONSTRAINT `hoadonban_ibfk_3` FOREIGN KEY (`MaCH`) REFERENCES `cuahang` (`MaCH`);

--
-- Constraints for table `kho`
--
ALTER TABLE `kho`
  ADD CONSTRAINT `kho_ibfk_1` FOREIGN KEY (`MaCH`) REFERENCES `cuahang` (`MaCH`),
  ADD CONSTRAINT `kho_ibfk_2` FOREIGN KEY (`MaSP`) REFERENCES `sanpham` (`MaSP`);

--
-- Constraints for table `nhanvien`
--
ALTER TABLE `nhanvien`
  ADD CONSTRAINT `nhanvien_ibfk_1` FOREIGN KEY (`MaCH`) REFERENCES `cuahang` (`MaCH`);

--
-- Constraints for table `phieunhap`
--
ALTER TABLE `phieunhap`
  ADD CONSTRAINT `phieunhap_ibfk_1` FOREIGN KEY (`MaNCC`) REFERENCES `nhacungcap` (`MaNCC`),
  ADD CONSTRAINT `phieunhap_ibfk_2` FOREIGN KEY (`MaNV`) REFERENCES `nhanvien` (`MaNV`),
  ADD CONSTRAINT `phieunhap_ibfk_3` FOREIGN KEY (`MaCH`) REFERENCES `cuahang` (`MaCH`);

--
-- Constraints for table `sanpham`
--
ALTER TABLE `sanpham`
  ADD CONSTRAINT `sanpham_ibfk_1` FOREIGN KEY (`MaDM`) REFERENCES `danhmuc` (`MaDM`);

--
-- Constraints for table `taikhoan`
--
ALTER TABLE `taikhoan`
  ADD CONSTRAINT `taikhoan_ibfk_1` FOREIGN KEY (`MaNV`) REFERENCES `nhanvien` (`MaNV`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
