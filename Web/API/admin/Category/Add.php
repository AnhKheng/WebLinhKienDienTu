<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");

    require_once "../../Config/db_config.php";
    require_once "../../../Includes/Category.php";

    $category = new Category($connect);

    // Lấy dữ liệu từ POST
    $MaDM = isset($_POST['idDM']) ? trim($_POST['idDM']) : '';
    $TenDM = isset($_POST['nameDM']) ? trim($_POST['nameDM']) : '';

    // Kiểm tra dữ liệu
    if (!empty($MaDM) && !empty($TenDM)) {
        if ($category->add($MaDM, $TenDM)) {
            echo json_encode([
                "status" => "success",
                "message" => "Thêm danh mục thành công!"
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "Không thể thêm danh mục. Có thể mã đã tồn tại."
            ]);
        }
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Thiếu dữ liệu MaDM hoặc TenDM! MaDM: $MaDM, TenDM: $TenDM"
        ]);
    }
?>