<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once "../Config/db_config.php";   // Kết nối database
require_once "../../Includes/Stores.php"; // Class xử lý cửa hàng

$stores = new Stores($connect);

// Nhận action từ GET hoặc POST
$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    // ==== 1️⃣ LẤY DANH SÁCH CỬA HÀNG ====
    case 'getAll':
        $result = $stores->getAll();
        echo json_encode($result);
        break;

    // ==== 2️⃣ LẤY THÔNG TIN THEO MÃ CỬA HÀNG ====
    case 'get_by_id':
        $MaCH = $_GET['MaCH'] ?? '';
        if (empty($MaCH)) {
            echo json_encode(["status" => "error", "message" => "Thiếu mã cửa hàng!"]);
            exit;
        }
        $result = $stores->getById($MaCH);
        echo json_encode($result);
        break;

    
    // ==== ACTION KHÔNG HỢP LỆ ====
    default:
        echo json_encode(["status" => "error", "message" => "Action không hợp lệ hoặc chưa được gửi!"]);
        break;
}
?>
