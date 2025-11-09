<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
require_once "../Config/db_config.php";      
require_once "../../Includes/Dashboard.php";

// $connect nên được tạo trong db_config.php (mysqli)
if (!isset($connect) || !$connect) {
    echo json_encode(["success" => false, "message" => "Kết nối DB không hợp lệ"]);
    exit;
}

$dashboard = new Dashboard($connect);

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'get_dashboard':
        $data = $dashboard->getDashboardData();
        echo json_encode([
            "success" => true,
            "data" => $data
        ]);
        break;

    default:
        echo json_encode([
            "success" => false,
            "message" => "Hành động không hợp lệ!"
        ]);
        break;
}
?>
