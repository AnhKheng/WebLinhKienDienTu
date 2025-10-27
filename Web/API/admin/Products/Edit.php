<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: PUT, POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");

require_once "../../Config/db_config.php";
require_once "../../../Includes/Products.php";

$product = new Product($connect);

// 🧩 Đọc dữ liệu JSON gửi lên
$data = json_decode(file_get_contents("php://input"), true);

if (
    isset($data["MaSP"]) &&
    isset($data["TenSP"]) &&
    isset($data["MaDM"]) &&
    isset($data["DonGia"]) &&
    isset($data["MoTa"]) &&
    isset($data["TrangThai"])
) {
    // Gán dữ liệu
    $MaSP = $data["MaSP"];
    $TenSP = $data["TenSP"];
    $MaDM = $data["MaDM"];
    $DonGia = floatval($data["DonGia"]);
    $MoTa = $data["MoTa"];
    $TrangThai = $data["TrangThai"];
    $HinhAnh = $data["HinhAnh"] ?? null;

    // 🧩 Gọi hàm cập nhật sản phẩm
    $result = $product->update($MaSP, $TenSP, $MaDM, $DonGia, $MoTa, $TrangThai, $HinhAnh);

    if ($result) {
        echo json_encode([
            "status" => "success",
            "message" => "Cập nhật sản phẩm thành công."
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Không thể cập nhật sản phẩm. Vui lòng kiểm tra lại."
        ]);
    }
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Thiếu dữ liệu đầu vào."
    ]);
}
?>
