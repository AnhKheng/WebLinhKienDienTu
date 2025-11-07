<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once "../Config/db_config.php";
require_once "../../Includes/Supplier.php";

$supplier = new Supplier($connect);
$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    // lấy tất cả
    case "getAll":
        $data = $supplier->getAll();
        echo json_encode(["status" => "success", "data" => $data]);
    break;

    // Lấy danh sách hoặc 1
    case 'view':
        $MaNCC = trim($_GET['MaNCC'] ?? '');
        if ($MaNCC) {
            $data = $supplier->getOne($MaNCC);
            echo json_encode(["status" => $data ? "success" : "error", "data" => $data]);
        } else {
            echo json_encode(["status" => "success", "data" => $supplier->getAll()]);
        }
        break;

    // Thêm nhà cung cấp
    case 'add':
        $TenNCC = trim($_POST['name'] ?? '');
        $DiaChi = trim($_POST['address'] ?? '');
        $SoDienThoai = trim($_POST['phone'] ?? '');

        if ($TenNCC != '') {
            echo json_encode(
                $supplier->add($TenNCC, $DiaChi, $SoDienThoai)
                ? ["status" => "success", "message" => "Thêm nhà cung cấp thành công!"]
                : ["status" => "error", "message" => "Không thể thêm nhà cung cấp!"]
            );
        }
        break;

    // Xóa nhà cung cấp
    case 'delete':
        $MaNCC = trim($_POST['idSup'] ?? '');
        echo json_encode(
            $supplier->delete($MaNCC)
            ? ["status" => "success", "message" => "Xóa nhà cung cấp thành công!"]
            : ["status" => "error", "message" => "Không thể xóa nhà cung cấp!"]
        );
        break;

    // Cập nhật nhà cung cấp
    case 'update':
        $MaNCC = trim($_POST['idSup'] ?? '');
        $TenNCC = trim($_POST['name'] ?? '');
        $DiaChi = trim($_POST['address'] ?? '');
        $SoDienThoai = trim($_POST['phone'] ?? '');

        echo json_encode(
            $supplier->update($MaNCC, $TenNCC, $DiaChi, $SoDienThoai)
            ? ["status" => "success", "message" => "Cập nhật nhà cung cấp thành công!"]
            : ["status" => "error", "message" => "Không thể cập nhật nhà cung cấp!"]
        );
        break;

    default:
        echo json_encode(["status" => "error", "message" => "Action không hợp lệ"]);
        break;
}
