
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once("../../Config/db_config.php");
require_once("../../../Includes/Invoices.php");

$hoadon = new HoaDon($connect);
$result = $hoadon->getAll();

if ($result && $result->num_rows > 0) {
    $list = [];
    while ($row = $result->fetch_assoc()) {
        $list[] = [
            "MaHD" => $row["MaHD"],
            "NgayBan" => $row["NgayBan"],
            "MaNV" => $row["MaNV"],
            //"TenNV" => isset($row["TenNV"]) ? $row["TenNV"] : null,
            "MaKH" => $row["MaKH"],
           // "TenKH" => isset($row["TenKH"]) ? $row["TenKH"] : null,
            "MaCH" => $row["MaCH"],
           // "TenCH" => isset($row["TenCH"]) ? $row["TenCH"] : null,
            "TongTien" => floatval($row["TongTien"])
        ];
    }

    echo json_encode([
        "status" => "success",
        "data" => $list
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Không có hóa đơn nào."
    ]);
}
?>
