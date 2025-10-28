<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once "../../Config/db_config.php";
require_once "../../../Includes/Category.php";

// Tạo đối tượng Category
$category = new Category($connect);

// Lấy mã danh mục từ query string
$MaDM = isset($_GET['MaDM']) ? trim($_GET['MaDM']) : '';

if (empty($MaDM)) {
    echo json_encode([
        "status" => "error",
        "message" => "Thiếu tham số MaDM! $MaDM"
    ]);
    exit;
}

$result = $category->getOne($MaDM);

$result = $category->getOne($MaDM);

if ($result) {
    echo json_encode([
        "status" => "success",
        "data" => [
            "MaDM" => $result["MaDM"],
            "TenDM" => $result["TenDM"]
        ]
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Không tìm thấy danh mục có mã: $MaDM"
    ]);
}
?>
