USE `shop_linhkien`;

-- Bảng: tbl_danhmuc
INSERT INTO `tbl_danhmuc` (`MaDM`, `TenDM`) VALUES
('DM01', 'Chuột máy tính'),
('DM02', 'Bàn phím'),
('DM03', 'Tai nghe'),
('DM04', 'Màn hình'),
('DM05', 'Linh kiện khác');

-- Bảng: tbl_sanpham
INSERT INTO `tbl_sanpham` (`MaSP`, `TenSP`, `MaDM`, `DonGia`, `MoTa`, `TrangThai`, `HinhAnh`) VALUES
('SP01', 'Chuột Logitech G102', 'DM01', 350000, 'Chuột gaming có cảm biến 8000 DPI, phù hợp học tập và giải trí.', b'1', 'c1.png'),
('SP02', 'Chuột Logitech G304 Wireless', 'DM01', 650000, 'Chuột không dây siêu nhẹ, độ trễ thấp Lightspeed.', b'1', 'c2.png'),
('SP03', 'Chuột Logitech G502 Hero', 'DM01', 890000, 'Chuột gaming nhiều nút macro, cảm biến HERO 25K.', b'1', 'c3.png'),
('SP04', 'Chuột Razer DeathAdder Essential', 'DM01', 450000, 'Thiết kế công thái học, cảm biến chính xác.', b'1', 'c4.png'),
('SP05', 'Chuột Razer Viper Mini', 'DM01', 690000, 'Siêu nhẹ, switch quang học tốc độ cao.', b'1', 'c5.png'),
('SP06', 'Bàn phím cơ DareU EK1280', 'DM02', 790000, 'Bàn phím cơ switch Blue, LED RGB.', b'1', 'bp1.png'),
('SP07', 'Tai nghe Logitech G331', 'DM03', 950000, 'Âm thanh vòm 7.1, mic lọc tiếng ồn.', b'1', 'tn1.png'),
('SP08', 'Màn hình Samsung 24 inch', 'DM04', 2390000, 'Tấm nền IPS, tần số quét 75Hz.', b'1', 'mh1.png'),
('SP09', 'Mainboard Asus Prime B660M', 'DM05', 3590000, 'Hỗ trợ Intel Gen 12, DDR4.', b'1', 'lk1.png'),
('SP10', 'RAM Kingston Fury 8GB DDR4', 'DM05', 650000, 'Bus 3200MHz, tản nhiệt hiệu quả.', b'1', 'lk2.png');

-- Bảng: tbl_nhacungcap
INSERT INTO `tbl_nhacungcap` (`MaNCC`, `TenNCC`, `DiaChi`, `SoDienThoai`) VALUES
('NCC01', 'Công ty Phân Phối Logitech VN', 'TP Hồ Chí Minh', '0281234567'),
('NCC02', 'Công ty DareU Việt Nam', 'Hà Nội', '0249876543'),
('NCC03', 'Công ty Samsung Display', 'Bình Dương', '0274123123'),
('NCC04', 'Công ty Asus Việt Nam', 'Hồ Chí Minh', '0282223333');

-- Bảng: tbl_cuahang
INSERT INTO `tbl_cuahang` (`MaCH`, `TenCH`, `DiaChi`, `SoDienThoai`) VALUES
('CH01', 'Cửa hàng Linh Kiện Khang Store', 'Bình Đức, Long Xuyên, An Giang', '0385227825'),
('CH02', 'Chi nhánh Cần Thơ', 'Ninh Kiều, Cần Thơ', '0905123456');

-- Bảng: tbl_nhanvien
INSERT INTO `tbl_nhanvien` (`MaNV`, `TenNV`, `GioiTinh`, `NgaySinh`, `SoDienThoai`, `MaCH`) VALUES
('NV01', 'Trần Vĩ Khang', 'Nam', '2004-04-10', '0385227825', 'CH01'),
('NV02', 'Nguyễn Văn Bình', 'Nam', '2001-02-15', '0912345678', 'CH01'),
('NV03', 'Lê Thị Hoa', 'Nữ', '1999-11-22', '0934556677', 'CH02');

-- Bảng: tbl_taikhoan
INSERT INTO `tbl_taikhoan` (`TenDangNhap`, `MatKhau`, `MaNV`, `VaiTro`) VALUES
('admin', '123456', 'NV01', 'QuanTri'),
('nv_binh', '123456', 'NV02', 'NhanVien'),
('nv_hoa', '123456', 'NV03', 'NhanVien');

-- Bảng: tbl_khachhang
INSERT INTO `tbl_khachhang` (`MaKH`, `TenKH`, `SoDienThoai`, `DiaChi`) VALUES
('KH01', 'Nguyễn Minh', '0911222333', 'An Giang'),
('KH02', 'Trần Hậu', '0988777666', 'Cần Thơ'),
('KH03', 'Phạm Lan', '0909888777', 'Đồng Tháp');

-- Bảng: tbl_kho
INSERT INTO `tbl_kho` (`MaCH`, `MaSP`, `SoLuongTon`) VALUES
('CH01', 'SP01', 20),
('CH01', 'SP02', 35),
('CH01', 'SP03', 10),
('CH01', 'SP04', 12),
('CH01', 'SP05', 25),
('CH01', 'SP06', 15),
('CH01', 'SP07', 8),
('CH01', 'SP08', 6),
('CH01', 'SP09', 100),
('CH01', 'SP10', 18);

-- Bảng: tbl_phieunhap
INSERT INTO `tbl_phieunhap` (`MaPN`, `NgayNhap`, `MaNCC`, `MaNV`, `MaCH`, `TongTien`) VALUES
('PN01', '2025-10-01 08:30:00', 'NCC01', 'NV01', 'CH01', 3500000),
('PN02', '2025-10-02 10:00:00', 'NCC02', 'NV02', 'CH01', 4200000),
('PN03', '2025-10-03 09:45:00', 'NCC04', 'NV03', 'CH02', 12000000);

-- Bảng: tbl_chitietphieunhap
INSERT INTO `tbl_chitietphieunhap` (`MaPN`, `MaSP`, `SoLuong`, `DonGiaNhap`) VALUES
('PN01', 'SP01', 10, 300000),
('PN01', 'SP02', 5, 600000),
('PN02', 'SP06', 10, 700000),
('PN02', 'SP07', 5, 900000),
('PN03', 'SP08', 10, 2200000);

-- Bảng: tbl_hoadonban
INSERT INTO `tbl_hoadonban` (`MaHD`, `NgayBan`, `MaNV`, `MaKH`, `MaCH`, `TongTien`) VALUES
('HD01', '2025-10-05 09:30:00', 'NV01', 'KH01', 'CH01', 890000),
('HD02', '2025-10-06 11:45:00', 'NV02', 'KH02', 'CH01', 1350000),
('HD03', '2025-10-07 16:15:00', 'NV03', 'KH03', 'CH02', 5200000);

-- Bảng: tbl_chitiethoadon
INSERT INTO `tbl_chitiethoadon` (`MaHD`, `MaSP`, `SoLuong`, `DonGia`) VALUES
('HD01', 'SP01', 1, 350000),
('HD01', 'SP02', 1, 650000),
('HD02', 'SP05', 1, 690000),
('HD02', 'SP07', 1, 950000),
('HD03', 'SP08', 2, 2390000);
