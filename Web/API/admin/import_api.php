<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once("../Config/db_config.php");
require_once("../../Includes/Import.php");

$hoadonnhap = new HoaDonNhap($connect);
$action = $_GET['action'] ?? 'view_all';

//  LẤY MÃ HÓA ĐƠN MỚI TỰ ĐỘNG

if ($action == 'getNewCode') {
    $query = "SELECT MaPN FROM tbl_phieunhap ORDER BY MaPN DESC LIMIT 1";
    $result = mysqli_query($connect, $query);
    $row = mysqli_fetch_assoc($result);

    if ($row) {
        $lastCode = $row['MaPN']; // ví dụ: HD06
        $num = (int)substr($lastCode, 2) + 1;
        $newCode = "PN" . str_pad($num, 2, "0", STR_PAD_LEFT);
    } else {
        $newCode = "PN01";
    }

    echo json_encode(["status" => "success", "newCode" => $newCode]);
    exit;
}

switch ($action) {
  // ✅ Xem danh sách
  case 'view_all':
    $result = $hoadonnhap->getAll();
    $list = [];
    if ($result && $result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        $list[] = $row;
      }
      echo json_encode(["status" => "success", "data" => $list]);
    } else {
      echo json_encode(["status" => "error", "message" => "Không có hóa đơn nhập nào."]);
    }
    break;

// them
  case 'add':
    $input = json_decode(file_get_contents("php://input"), true);
    $added = $hoadonnhap->add($input);

    if ($added === true) {
    echo json_encode(["status" => "success", "message" => "Thêm hóa đơn nhập thành công"]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Thêm hóa đơn nhập thất bại",
            "error_detail" => $hoadonnhap->lastError ?? "Không rõ nguyên nhân"
        ]);
    }
    break;

  // ✅ Xóa hóa đơn nhập
  case 'delete':
    $maPN = $_GET['maPN'] ?? '';
    if ($hoadonnhap->delete($maPN)) {
      echo json_encode(["status" => "success", "message" => "Đã xóa hóa đơn nhập"]);
    } else {
      echo json_encode(["status" => "error", "message" => "Xóa thất bại"]);
    }
    break;
    
    //Xem chi tiết các sản phẩm trong hóa đơn
case 'viewDetail':
  $maPN = $_GET['maPN'] ?? '';
  if (empty($maPN)) {
    echo json_encode(["status" => "error", "message" => "Thiếu mã hóa đơn"]);
    exit;
  }
  $chiTiet = $hoadonnhap->getChiTiet($maPN);
  if (!empty($chiTiet)) {
    echo json_encode(["status" => "success", "data" => $chiTiet]);
  } else {
    echo json_encode(["status" => "error", "message" => "Không có chi tiết hóa đơn  nhập"]);
  }
  break;

  default:
    echo json_encode(["status" => "error", "message" => "Hành động không hợp lệ"]);
}



?>