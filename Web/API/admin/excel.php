<?php
require_once "../Config/db_config.php"; // file MySQLi của bạn
require_once "../../../vendor/autoload.php"; // autoload của Composer

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

if (!isset($_GET['MaHD'])) {
    die("Thiếu mã hóa đơn.");
}

$maHD = $_GET['MaHD'];

// 🔹 Dùng kết nối mysqli từ file db_config.php
global $connect; // lấy biến $connect đã tạo sẵn
if (!$connect) {
    die("Không kết nối được CSDL");
}

// 🔹 Lấy thông tin hóa đơn + khách hàng + cửa hàng
$sqlInfo = "
SELECT hd.MaHD, hd.NgayBan, hd.TongTien,
       nv.TenNV, kh.TenKH, kh.SoDienThoai, kh.DiaChi,
       ch.TenCH, ch.DiaChi AS DiaChiCH
FROM tbl_hoadonban hd
LEFT JOIN tbl_nhanvien nv ON hd.MaNV = nv.MaNV
LEFT JOIN tbl_khachhang kh ON hd.MaKH = kh.MaKH
LEFT JOIN tbl_cuahang ch ON hd.MaCH = ch.MaCH
WHERE hd.MaHD = ?
";

$stmt = $connect->prepare($sqlInfo);
$stmt->bind_param("s", $maHD);
$stmt->execute();
$result = $stmt->get_result();
$info = $result->fetch_assoc();

if (!$info) {
    die("Không tìm thấy hóa đơn.");
}

// 🔹 Lấy chi tiết sản phẩm
$sqlCT = "
SELECT cthd.MaSP, sp.TenSP, cthd.SoLuong, cthd.DonGia,
       (cthd.SoLuong * cthd.DonGia) AS ThanhTien
FROM tbl_chitiethoadon cthd
JOIN tbl_sanpham sp ON cthd.MaSP = sp.MaSP
WHERE cthd.MaHD = ?
";

$stmt = $connect->prepare($sqlCT);
$stmt->bind_param("s", $maHD);
$stmt->execute();
$result = $stmt->get_result();
$details = $result->fetch_all(MYSQLI_ASSOC);

// ===============================================
// 🧾 Tạo file Excel
// ===============================================
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Tiêu đề
$sheet->setCellValue('A1', 'HÓA ĐƠN BÁN HÀNG');
$sheet->mergeCells('A1:E1');
$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);

// Thông tin chung
$sheet->fromArray([
    ['Mã HĐ:', $info['MaHD']],
    ['Ngày bán:', $info['NgayBan']],
    ['Nhân viên:', $info['TenNV']],
    ['Khách hàng:', $info['TenKH']],
    ['SĐT KH:', $info['SoDienThoai']],
    ['Địa chỉ KH:', $info['DiaChi']],
    ['Cửa hàng:', $info['TenCH']],
    ['Địa chỉ CH:', $info['DiaChiCH']],
], null, 'A3', true);

// Dòng trống
$sheet->setCellValue('A11', '');

// Bảng chi tiết
$sheet->fromArray(
    [['STT', 'Mã SP', 'Tên sản phẩm', 'Số lượng', 'Đơn giá', 'Thành tiền']],
    null,
    'A12'
);

$row = 13;
$stt = 1;
foreach ($details as $item) {
    $sheet->fromArray([
        $stt++,
        $item['MaSP'],
        $item['TenSP'],
        $item['SoLuong'],
        $item['DonGia'],
        $item['ThanhTien']
    ], null, 'A' . $row);
    $row++;
}

// Tổng tiền
$sheet->setCellValue('E' . $row, 'Tổng cộng:');
$sheet->setCellValue('F' . $row, $info['TongTien']);

// Ghi đậm
$sheet->getStyle("A12:F12")->getFont()->setBold(true);
$sheet->getStyle("E{$row}:F{$row}")->getFont()->setBold(true);

// Auto width
foreach (range('A', 'F') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// Xuất file
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="HoaDon_' . $maHD . '.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>
