USE `shop_linhkien`;

-- Bảng: tbl_danhmuc
INSERT INTO `tbl_danhmuc` (`MaDM`, `TenDM`) VALUES
('DM01', 'Chuột máy tính'),
('DM02', 'Bàn phím'),
('DM03', 'Tai nghe'),
('DM04', 'Màn hình'),
('DM05', 'Laptop'),
('DM06', 'Laptop Gaming'),
('DM07', 'PC GVN'),
('DM08', 'Main, CPU, VGA'),
('DM09', 'Case, Nguồn, Tản'),
('DM10', 'Ổ cứng, RAM, Thẻ nhớ'),
('DM11', 'Loa, Micro, Webcam'),
('DM12', 'Chuột + Lót chuột'),
('DM13', 'Bàn ghế'),
('DM14', 'Handheld, Console'),
('DM15', 'Phụ kiện (Hub, Sạc, Cáp,...)'),
('DM16', 'Dịch vụ và thông tin khác');
SET FOREIGN_KEY_CHECKS = 1; -- Bật lại kiểm tra khóa ngoại

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
('SP11', 'Laptop Dell Inspiron 15', 'DM05', 12900000, 'Laptop văn phòng, Core i5, RAM 8GB.', b'1', 'lap1.png'),
('SP12', 'Laptop Gaming ASUS ROG Strix', 'DM06', 25900000, 'GPU RTX 3060, màn hình 144Hz.', b'1', 'lapG1.png'),
('SP13', 'PC GVN Custom i7-12700', 'DM07', 18900000, 'PC lắp ráp, hiệu năng cao.', b'1', 'pc1.png'),
('SP14', 'Mainboard MSI B550-A Pro', 'DM08', 2790000, 'Hỗ trợ AMD Ryzen, PCIe 4.0.', b'1', 'main1.png'),
('SP15', 'Case Deepcool Matrexx 55', 'DM09', 1290000, 'Thiết kế đẹp, tản nhiệt tốt.', b'1', 'case1.png'),
('SP16', 'Ổ cứng SSD Samsung 500GB', 'DM10', 1190000, 'Tốc độ đọc 550MB/s.', b'1', 'hard1.png'),
('SP17', 'Loa JBL Charge 5', 'DM11', 2990000, 'Loa Bluetooth chống nước.', b'1', 'loa1.png'),
('SP18', 'Lót chuột SteelSeries QcK', 'DM12', 250000, 'Kích thước lớn, chống trơn trượt.', b'1', 'lot1.png'),
('SP19', 'Bàn ghế DXRacer Racing', 'DM13', 7990000, 'Ghế gaming, hỗ trợ lưng tốt.', b'1', 'bg1.png'),
('SP20', 'Handheld Steam Deck 64GB', 'DM14', 10900000, 'Thiết bị chơi game di động.', b'1', 'hd1.png'),
('SP21', 'Phụ kiện Hub USB 4 cổng', 'DM15', 350000, 'Tương thích đa dạng thiết bị.', b'1', 'pk1.png'),
('SP22', 'Dịch vụ bảo hành 1 năm', 'DM16', 500000, 'Bảo hành toàn diện cho sản phẩm.', b'1', 'dv1.png'),
('SP23', 'Laptop HP Pavilion 14', 'DM05', 14900000, 'Core i7, màn hình Full HD.', b'1', 'lap2.png'),
('SP24', 'Laptop Gaming MSI Katana', 'DM06', 27900000, 'RTX 3070, tản nhiệt hiệu quả.', b'1', 'lapG2.png'),
('SP25', 'PC GVN Ryzen 5 5600X', 'DM07', 15900000, 'Hiệu năng ổn định, giá tốt.', b'1', 'pc2.png'),
('SP26', 'CPU AMD Ryzen 7 5800X', 'DM08', 5990000, '8 nhân 16 luồng, hiệu suất cao.', b'1', 'cpu1.png'),
('SP27', 'Nguồn Corsair RM750x', 'DM09', 2290000, 'Nguồn 80+ Gold, 750W.', b'1', 'ng1.png'),
('SP28', 'RAM Corsair Vengeance 16GB', 'DM10', 1290000, 'Bus 3600MHz, hiệu năng mạnh.', b'1', 'ram1.png'),
('SP29', 'Micro Blue Yeti', 'DM11', 2490000, 'Mic thu âm chuyên nghiệp.', b'1', 'mic1.png'),
('SP30', 'Lót chuột Razer Gigantus', 'DM12', 350000, 'Bề mặt vải, chống mài mòn.', b'1', 'lot2.png'),
('SP31', 'Bàn ghế Secretlab Titan', 'DM13', 8990000, 'Ghế gaming cao cấp, độ bền cao.', b'1', 'bg2.png'),
('SP32', 'Console Nintendo Switch OLED', 'DM14', 7990000, 'Màn hình 7 inch, chơi game di động.', b'1', 'cs1.png'),
('SP33', 'Phụ kiện Sạc không dây 15W', 'DM15', 450000, 'Tương thích iPhone, Android.', b'1', 'pk2.png'),
('SP34', 'Dịch vụ cài đặt phần mềm', 'DM16', 300000, 'Cài đặt và tối ưu hóa hệ thống.', b'1', 'dv2.png'),
('SP35', 'Laptop Lenovo IdeaPad 3', 'DM05', 11900000, 'Core i3, RAM 8GB, học tập tốt.', b'1', 'lap3.png'),
('SP36', 'Laptop Gaming Acer Nitro 5', 'DM06', 22900000, 'RTX 3050, màn hình 120Hz.', b'1', 'lapG3.png'),
('SP37', 'PC GVN i9-12900K', 'DM07', 25900000, 'Hiệu năng mạnh mẽ, chơi game mượt.', b'1', 'pc3.png'),
('SP38', 'VGA NVIDIA RTX 3060', 'DM08', 7990000, '6GB GDDR6, ray tracing tốt.', b'1', 'vga1.png'),
('SP39', 'Tản nhiệt nước NZXT Kraken', 'DM09', 1990000, 'Tản nhiệt AIO, RGB đẹp mắt.', b'1', 'tan1.png'),
('SP40', 'Thẻ nhớ SanDisk 128GB', 'DM10', 350000, 'Tốc độ cao, phù hợp camera.', b'1', 'the1.png'),
('SP41', 'Webcam Logitech C920', 'DM11', 1290000, 'Full HD, mic tích hợp.', b'1', 'cam1.png'),
('SP42', 'Lót chuột Logitech G440', 'DM12', 450000, 'Kích thước lớn, chất liệu cao cấp.', b'1', 'lot3.png'),
('SP43', 'Bàn ghế Anda Seat', 'DM13', 6990000, 'Ghế gaming giá hợp lý.', b'1', 'bg3.png');

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
('CH01', 'SP08', 6);

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

-- Bảng: tbl_taikhoankhachhang
INSERT INTO `tbl_taikhoankhachhang` 
(`MaKH`, `LoaiDangNhap`, `TenDangNhap`, `Email`, `MatKhau`)VALUES
('KH01', 'local', 'nguyenminh01', 'nguyenminh@gmail.com', '123456'),
('KH02', 'google', NULL, 'tran.hau@gmail.com', NULL),
('KH03', 'local', 'phamlan_90', 'phamlan@yahoo.com', 'lanpass');
