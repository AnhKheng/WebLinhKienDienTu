<?php
require_once "../Config/db_config.php"; 
require_once "../../../vendor/autoload.php"; 

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


$maPN = $_GET['MaPN'] ?? '';
if (empty($maPN)) {
    die("Thiếu mã phiếu nhập.");
}

global $connect;
if (!$connect) {
    die("Không kết nối được CSDL");
}


$sqlInfo = "
SELECT pn.MaPN, pn.NgayNhap, pn.TongTien,
       ncc.TenNCC, ncc.DiaChi AS DiaChiNCC, ncc.SoDienThoai AS SDTNCC,
       nv.TenNV,
       ch.TenCH, ch.DiaChi AS DiaChiCH
FROM tbl_phieunhap pn
LEFT JOIN tbl_nhacungcap ncc ON pn.MaNCC = ncc.MaNCC
LEFT JOIN tbl_nhanvien nv ON pn.MaNV = nv.MaNV
LEFT JOIN tbl_cuahang ch ON pn.MaCH = ch.MaCH
WHERE pn.MaPN = ?
";

$stmt = $connect->prepare($sqlInfo);
$stmt->bind_param("s", $maPN);
$stmt->execute();
$result = $stmt->get_result();
$info = $result->fetch_assoc();

if (!$info) {
    die("Không tìm thấy phiếu nhập.");
}


$sqlCT = "
SELECT ctpn.MaSP, sp.TenSP, ctpn.SoLuong, ctpn.DonGiaNhap,
       (ctpn.SoLuong * ctpn.DonGiaNhap) AS ThanhTien
FROM tbl_chitietphieunhap ctpn
JOIN tbl_sanpham sp ON ctpn.MaSP = sp.MaSP
WHERE ctpn.MaPN = ?
";
$stmt = $connect->prepare($sqlCT);
$stmt->bind_param("s", $maPN);
$stmt->execute();
$result = $stmt->get_result();
$details = $result->fetch_all(MYSQLI_ASSOC);


$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Tiêu đề
$sheet->setCellValue('A1', 'PHIẾU NHẬP HÀNG');
$sheet->mergeCells('A1:F1');
$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);

// Thông tin chung
$sheet->fromArray([
    ['Mã phiếu nhập:', $info['MaPN']],
    ['Ngày nhập:', $info['NgayNhap']],
    ['Nhân viên nhập:', $info['TenNV']],
    ['Nhà cung cấp:', $info['TenNCC']],
    ['SĐT NCC:', $info['SDTNCC']],
    ['Địa chỉ NCC:', $info['DiaChiNCC']],
    ['Cửa hàng:', $info['TenCH']],
    ['Địa chỉ cửa hàng:', $info['DiaChiCH']],
], null, 'A3', true);

// Dòng trống
$sheet->setCellValue('A11', '');

// Bảng chi tiết phiếu nhập
$sheet->fromArray(
    [['STT', 'Mã SP', 'Tên sản phẩm', 'Số lượng', 'Đơn giá nhập', 'Thành tiền']],
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
        number_format($item['DonGiaNhap'], 0, ',', '.'),
        number_format($item['ThanhTien'], 0, ',', '.')
    ], null, 'A' . $row);
    $row++;
}

//  Tổng tiền
$sheet->setCellValue('E' . $row, 'Tổng cộng:');
$sheet->setCellValue('F' . $row, number_format($info['TongTien'], 0, ',', '.') . ' ₫');

// In đậm tiêu đề và tổng
$sheet->getStyle("A12:F12")->getFont()->setBold(true);
$sheet->getStyle("E{$row}:F{$row}")->getFont()->setBold(true);

// Auto width cho tất cả các cột
foreach (range('A', 'F') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}


header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="PhieuNhap_' . $maPN . '.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>
