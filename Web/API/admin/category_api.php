<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once "../Config/db_config.php";
require_once "../../Includes/Category.php";

$category = new Category($connect);

// Nhận tham số action từ GET hoặc POST
$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    // ==== XEM DANH SÁCH DANH MỤC ====
    case 'view':
        $MaDM = trim($_GET['MaDM'] ?? '');
        if ($MaDM) {
            $result = $category->getOne($MaDM);
            if ($result) {
                echo json_encode(["status" => "success", "data" => $result]);
            } else {
                echo json_encode(["status" => "error", "message" => "Không tìm thấy danh mục."]);
            }
        } else {
            $list = $category->getAll();
            echo json_encode(["status" => "success", "data" => $list]);
        }
        break;

    // ==== THÊM DANH MỤC ====
    case 'add':
        $TenDM = trim($_POST['nameDM'] ?? '');

        if (!empty($TenDM)) {
            // Tự động sinh mã trong class Category
            if ($category->add($TenDM)) {
                echo json_encode([
                    "status" => "success",
                    "message" => "Thêm danh mục thành công!"
                ]);
            } else {
                echo json_encode([
                    "status" => "error",
                    "message" => "Không thể thêm danh mục. Vui lòng thử lại!"
                ]);
            }
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "Thiếu dữ liệu tên danh mục!"
            ]);
        }
        break;

    // ==== XÓA DANH MỤC ====
    case 'delete':
        $MaDM = trim($_POST['idDM'] ?? '');

        if (!empty($MaDM)) {
            if ($category->delete($MaDM)) {
                echo json_encode([
                    "status" => "success",
                    "message" => "Xóa danh mục thành công!"
                ]);
            } else {
                echo json_encode([
                    "status" => "error",
                    "message" => "Không thể xóa danh mục. Có thể mã không tồn tại hoặc đang được sử dụng."
                ]);
            }
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "Thiếu mã danh mục cần xóa!"
            ]);
        }
        break;

    // ==== CẬP NHẬT DANH MỤC ====
    case 'edit':
        $MaDM = trim($_POST['idDM'] ?? '');
        $TenDM = trim($_POST['nameDM'] ?? '');

        if (empty($MaDM) || empty($TenDM)) {
            echo json_encode([
                "status" => "error",
                "message" => "Thiếu dữ liệu MaDM hoặc TenDM!"
            ]);
            exit;
        }

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
        break;

    // ==== TRƯỜNG HỢP ACTION KHÔNG HỢP LỆ ====
    default:
        echo json_encode([
            "status" => "error",
            "message" => "Action không hợp lệ hoặc chưa được gửi!"
        ]);
        break;
}
?>
