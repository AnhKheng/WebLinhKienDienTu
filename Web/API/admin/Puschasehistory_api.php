<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");

require_once "../Config/db_config.php"; // kết nối DB
require_once "../../Includes/Invoices.php"; // class xử lý hóa đơn
require_once "../../Includes/Products.php"; // class xử lý sản phẩm

// khởi tạo kết nối
$hoaDon = new HoaDon($connect);
$product = new Product($connect);

$action = $_GET['action'] ?? '';

switch($action){
    case "getHistory":
        $MaKH = $_GET['MaKH'] ?? '';
        if(empty($MaKH)){
            echo json_encode(["status"=>"error","message"=>"Thiếu MaKH"]);
            exit;
        }

        // Lấy tất cả hóa đơn theo khách hàng
        $sql = "SELECT hd.MaHD, hd.NgayBan, cthd.MaSP, sp.TenSP, cthd.SoLuong, cthd.DonGia,
                       (cthd.SoLuong*cthd.DonGia) AS ThanhTien
                FROM tbl_hoadonban hd
                JOIN tbl_chitiethoadon cthd ON hd.MaHD = cthd.MaHD
                JOIN tbl_sanpham sp ON cthd.MaSP = sp.MaSP
                WHERE hd.MaKH = ? 
                ORDER BY hd.NgayBan DESC";

        $stmt = $connect->prepare($sql);
        $stmt->bind_param("s", $MaKH);
        $stmt->execute();
        $result = $stmt->get_result();

        $data = [];
        $tongTien = 0;
        while($row = $result->fetch_assoc()){
            $row["DonGia"] = floatval($row["DonGia"]);
            $row["ThanhTien"] = floatval($row["ThanhTien"]);
            $data[] = $row;
            $tongTien += $row["ThanhTien"];
        }

        echo json_encode([
            "status"=>"success",
            "MaKH"=>$MaKH,
            "TongTien"=>$tongTien,
            "count"=>count($data),
            "data"=>$data
        ]);
        break;

    default:
        echo json_encode(["status"=>"error","message"=>"Hành động không hợp lệ"]);
        break;
}
?>
