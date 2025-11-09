<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once "../Config/db_config.php";   
require_once "../../Includes/Statistical.php"; 

$cuaHang = new CuaHang($connect);

$action = $_GET['action'] ?? 'getStore';

switch ($action) {

    case 'getStatistical':
        $maCH = $_GET['MaCH'] ?? '';
        $tungay = $_GET['tungay'] ?? null;
        $denngay = $_GET['denngay'] ?? null;
        echo json_encode($cuaHang->getStatistical($maCH, $tungay, $denngay), JSON_UNESCAPED_UNICODE);
        break;
    
    case 'getInventory':
        $maCH = $_GET['MaCH'] ?? '';
        echo json_encode($cuaHang->getInventory($maCH), JSON_UNESCAPED_UNICODE);
        break;

    default:
        echo json_encode(["status" => "error", "message" => "Hành động không hợp lệ!"], JSON_UNESCAPED_UNICODE);
}

?>