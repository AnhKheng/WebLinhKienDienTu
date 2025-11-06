<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once "../Config/db_config.php";
require_once "../../Includes/Inventory.php";

$inventory = new Inventory($connect);

// Nhận tham số action từ GET hoặc POST
$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    // ==== XEM TOÀN BỘ TỒN KHO ====
    case 'view':
        $MaCH = trim($_GET['MaCH'] ?? '');
        $MaSP = trim($_GET['MaSP'] ?? '');
        $keyword = trim($_GET['keyword'] ?? '');
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        $offset = ($page - 1) * $limit;

        if (!empty($keyword)) {
            $data = $inventory->searchInventory($keyword, $offset, $limit);
            $total = $inventory->countSearch($keyword);
            $totalPages = ceil($total / $limit);
            echo json_encode([
                "status" => "success",
                "search" => $keyword,
                "page" => $page,
                "limit" => $limit,
                "total" => $total,
                "total_pages" => $totalPages,
                "data" => $data
            ]);
            break;
        }

        if (!empty($MaCH) && !empty($MaSP)) {
            $result = $inventory->getOne($MaCH, $MaSP);
            echo json_encode([
                "status" => $result ? "success" : "error",
                "data" => $result ?: null,
                "message" => $result ? "" : "Không tìm thấy dữ liệu tồn kho."
            ]);
            break;
        }

        if (!empty($MaCH)) {
            $data = $inventory->getByStore($MaCH);
            $total = count($data);
        } elseif (!empty($MaSP)) {
            $data = $inventory->getByProduct($MaSP);
            $total = count($data);
        } else {
            $data = $inventory->getPaged($offset, $limit);
            $total = $inventory->countAll();
        }

        $totalPages = ceil($total / $limit);
        echo json_encode([
            "status" => "success",
            "page" => $page,
            "limit" => $limit,
            "total" => $total,
            "total_pages" => $totalPages,
            "data" => $data
        ]);
        break;

    case 'countByStore':
        $MaCH = trim($_GET['MaCH'] ?? '');
        if (empty($MaCH)) {
            echo json_encode(["status" => "error", "message" => "Thiếu mã cửa hàng!"]);
            exit;
        }

        $result = $inventory->countByStore($MaCH);
        echo json_encode([
            "status" => "success",
            "store" => $result['TenCH'],
            "total" => (int)$result['total']
        ]);
        break;

    case 'getMaCH':
        session_start();
        if (isset($_SESSION['ma_ch'])) {
            echo json_encode(["status" => "success", "MaCH" => $_SESSION['ma_ch']]);
        } else {
            echo json_encode(["status" => "error", "message" => "Chưa đăng nhập hoặc chưa có session ma_ch"]);
        }
        break;

    default:
        echo json_encode([
            "status" => "error",
            "message" => "Action không hợp lệ hoặc chưa được gửi!"
        ]);
        break;
}
?>
