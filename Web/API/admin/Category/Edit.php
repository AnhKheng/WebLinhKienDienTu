<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once "../../Config/db_config.php";
require_once "../../../Includes/Category.php";

$category = new Category($connect);

// === Lấy dữ liệu từ POST ===
$MaDM = isset($_POST['idDM']) ? trim($_POST['idDM']) : '';
$TenDM = isset($_POST['nameDM']) ? trim($_POST['nameDM']) : '';

// === Kiểm tra dữ liệu đầu vào ===
if (empty($MaDM) || empty($TenDM)) {
    echo json_encode([
        "status" => "error",
        "message" => "Thiếu dữ liệu MaDM hoặc TenDM!"
    ]);
    exit;
}

// === Gọi hàm update từ model ===
if ($category->update($MaDM, $TenDM)) {
    echo json_encode([
        "status" => "success",
        "message" => "Cập nhật danh mục thành công!"
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Không thể cập nhật danh mục. Vui lòng kiểm tra lại!"
    ]);
}
?>
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once "../../Config/db_config.php";
require_once "../../../Includes/Category.php";

$category = new Category($connect);

// === Lấy dữ liệu từ POST ===
$MaDM = isset($_POST['idDM']) ? trim($_POST['idDM']) : '';
$TenDM = isset($_POST['nameDM']) ? trim($_POST['nameDM']) : '';

// === Kiểm tra dữ liệu đầu vào ===
if (empty($MaDM) || empty($TenDM)) {
    echo json_encode([
        "status" => "error",
        "message" => "Thiếu dữ liệu MaDM hoặc TenDM!"
    ]);
    exit;
}

// === Gọi hàm update từ model ===
if ($category->update($MaDM, $TenDM)) {
    echo json_encode([
        "status" => "success",
        "message" => "Cập nhật danh mục thành công!"
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Không thể cập nhật danh mục. Vui lòng kiểm tra lại!"
    ]);
}
?>
