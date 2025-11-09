<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once "../Config/db_config.php";      // Kết nối DB
require_once "../../Includes/Customer.php";  // Class xử lý logic

$customers = new Customers($connect);

// Nhận action từ GET hoặc POST
$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {

    case 'getAll':
        $result = $customers->getAll();
        echo json_encode([
            "status" => "success",
            "data" => $result
        ], JSON_UNESCAPED_UNICODE);
        break;

    case 'get_by_id':
        $maKH = $_GET['MaKH'] ?? $_POST['MaKH'] ?? '';
        if (empty($maKH)) {
            echo json_encode(["status" => "error", "message" => "Thiếu mã khách hàng!"], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $result = $customers->getById($maKH);
        if ($result) {
            echo json_encode(["status" => "success", "data" => $result], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(["status" => "error", "message" => "Không tìm thấy khách hàng!"], JSON_UNESCAPED_UNICODE);
        }
        break;

    case 'add':
        $input = json_decode(file_get_contents('php://input'), true);
        $TenKH = trim($input['TenKH'] ?? '');
        $SoDienThoai = trim($input['SoDienThoai'] ?? '');
        $DiaChi = trim($input['DiaChi'] ?? '');

        if (empty($TenKH)) {
            echo json_encode(["status" => "error", "message" => "Vui lòng nhập tên khách hàng!"], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $result = $customers->add($TenKH, $SoDienThoai, $DiaChi);
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
        break;
    case 'update': // Cập nhật khách hàng
        $input = json_decode(file_get_contents('php://input'), true);
        $MaKH = trim($input['MaKH'] ?? '');
        $TenKH = trim($input['TenKH'] ?? '');
        $SoDienThoai = trim($input['SoDienThoai'] ?? '');
        $DiaChi = trim($input['DiaChi'] ?? '');
        if (empty($MaKH) || empty($TenKH)) {
            echo json_encode(["status" => "error", "message" => "Thiếu mã hoặc tên khách hàng!"], JSON_UNESCAPED_UNICODE);
            exit;
        }
        $result = $customers->update($MaKH, $TenKH, $SoDienThoai, $DiaChi);
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
        break;

    case 'delete': // Xóa khách hàng
        $input = json_decode(file_get_contents('php://input'), true);
        $MaKH = trim($input['MaKH'] ?? $_GET['MaKH'] ?? '');
        if (empty($MaKH)) {
            echo json_encode(["status" => "error", "message" => "Thiếu mã khách hàng!"], JSON_UNESCAPED_UNICODE);
            exit;
        }
        $result = $customers->delete($MaKH);
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
        break;
    case 'getAllPaged':
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;

        $result = $customers->getAllPaged($page, $limit);
        echo json_encode([
            "status" => "success",
            "data" => $result['data'],
            "page" => $result['page'],
            "limit" => $result['limit'],
            "total" => $result['total'],
            "totalPages" => $result['totalPages']
        ], JSON_UNESCAPED_UNICODE);
        break;
    default:
        echo json_encode(["status" => "error", "message" => "Action không hợp lệ hoặc chưa gửi!"], JSON_UNESCAPED_UNICODE);
        break;
}
?>
