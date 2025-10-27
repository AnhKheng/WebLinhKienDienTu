<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once "../Config/db_config.php";
require_once "../../../includes/Product.php";

$product = new Product($conn);
$result = $product->getAll();

if ($result && $result->num_rows > 0) {
    $products = [];
    while ($row = $result->fetch_assoc()) {
        $products[] = [
            "MaSP" => $row["MaSP"],
            "TenSP" => $row["TenSP"],
            "MaDM" => $row["MaDM"],
            "TenDM" => $row["TenDM"],
            "DonGia" => floatval($row["DonGia"]),
            "MoTa" => $row["MoTa"],
            "TrangThai" => $row["TrangThai"] == 1 ? "Hoạt động" : "Ngừng bán",
            "HinhAnh" => $row["HinhAnh"]
        ];
    }
    echo json_encode(["status" => "success", "data" => $products]);
} else {
    echo json_encode(["status" => "error", "message" => "Không có sản phẩm nào."]);
}
?>
