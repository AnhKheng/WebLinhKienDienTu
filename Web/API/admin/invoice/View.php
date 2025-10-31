<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once("../../Config/db_config.php");
require_once("../../../Includes/Invoices.php");

$hoadon = new HoaDon($connect);
$action = $_GET['action'] ?? 'view_all';


switch ($action) {
  // ✅ Xem danh sách
  case 'view_all':
    $result = $hoadon->getAll();
    $list = [];
    if ($result && $result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        $list[] = $row;
      }
      echo json_encode(["status" => "success", "data" => $list]);
    } else {
      echo json_encode(["status" => "error", "message" => "Không có hóa đơn nào."]);
    }
    break;

  // ✅ Sửa hóa đơn
  case 'update':
    $input = json_decode(file_get_contents("php://input"), true);
    $updated = $hoadon->update($input);
    if ($updated) {
      echo json_encode(["status" => "success", "message" => "Cập nhật thành công"]);
    } else {
      echo json_encode(["status" => "error", "message" => "Cập nhật thất bại"]);
    }
    break;

  // ✅ Xóa hóa đơn
  case 'delete':
    $maHD = $_GET['MaHD'] ?? '';
    if ($hoadon->delete($maHD)) {
      echo json_encode(["status" => "success", "message" => "Đã xóa hóa đơn"]);
    } else {
      echo json_encode(["status" => "error", "message" => "Xóa thất bại"]);
    }
    break;
    //xem chi tiet hoa don
    // ✅ Xem chi tiết các sản phẩm trong hóa đơn
case 'viewDetail':
  $maHD = $_GET['MaHD'] ?? '';
  if (empty($maHD)) {
    echo json_encode(["status" => "error", "message" => "Thiếu mã hóa đơn"]);
    exit;
  }
  $chiTiet = $hoadon->getChiTiet($maHD);
  if (!empty($chiTiet)) {
    echo json_encode(["status" => "success", "data" => $chiTiet]);
  } else {
    echo json_encode(["status" => "error", "message" => "Không có chi tiết hóa đơn"]);
  }
  break;
// case 'add':
//     $input = json_decode(file_get_contents("php://input"), true);

//     if (!$input || empty($input['MaHD']) || empty($input['MaNV']) || empty($input['MaKH']) || empty($input['MaCH']) || empty($input['TongTien'])) {
//       echo json_encode(["status" => "error", "message" => "Thiếu dữ liệu cần thiết để thêm hóa đơn."]);
//       break;
//     }

//     $added = $hoadon->add($input);

//     if ($added) {
//       echo json_encode(["status" => "success", "message" => "Thêm hóa đơn thành công."]);
//     } else {
//       echo json_encode(["status" => "error", "message" => "Không thể thêm hóa đơn."]);
//     }
//     break;



  default:
    echo json_encode(["status" => "error", "message" => "Hành động không hợp lệ"]);
}
?>
