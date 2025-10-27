<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    require_once "../../Config/db_config.php";
    require_once "../../../Includes/Category.php";
    $category = new Category($connect);
    $result = $category->getAll();

    if ($result && $result->num_rows > 0) {
        $categories = [];
        while ($row = $result->fetch_assoc()) {
            $categories[] = [
                "MaDM" => $row["MaDM"],
                "TenDM" => $row["TenDM"]
            ];
        }
        echo json_encode(["status" => "success", "data" => $categories]);
    } else {
        echo json_encode(["status" => "error", "message" => "Không có danh mục nào."]);
    }
?>