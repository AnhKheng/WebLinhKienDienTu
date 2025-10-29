<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");

    require_once "../../Config/db_config.php";
    require_once "../../../Includes/Category.php";

    $category = new Category($connect);

    // Lấy dữ liệu từ POST (hoặc GET tùy theo frontend gửi)
    $MaDM = isset($_POST['idDM']) ? trim($_POST['idDM']) : '';

    // Kiểm tra dữ liệu hợp lệ
    if (!empty($MaDM)) {
        if ($category->delete($MaDM)) {
            echo json_encode([
                "status" => "success",
                "message" => "Xóa danh mục thành công!"
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "Không thể xóa danh mục. Có thể mã danh mục không tồn tại hoặc đang được sử dụng."
            ]);
        }
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Thiếu mã danh mục cần xóa!"
        ]);
    }
?>
