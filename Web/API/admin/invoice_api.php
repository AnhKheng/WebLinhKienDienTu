<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once("../Config/db_config.php");
require_once("../../Includes/Invoices.php");

$hoadon = new HoaDon($connect);
$action = $_GET['action'] ?? 'view_all';
// ==========================
// ✅ LẤY MÃ HÓA ĐƠN MỚI TỰ ĐỘNG
// ==========================
if ($action == 'getNewCode') {
    $query = "SELECT MaHD FROM tbl_hoadonban ORDER BY MaHD DESC LIMIT 1";
    $result = mysqli_query($connect, $query);
    $row = mysqli_fetch_assoc($result);

    if ($row) {
        $lastCode = $row['MaHD']; // ví dụ: HD06
        $num = (int)substr($lastCode, 2) + 1;
        $newCode = "HD" . str_pad($num, 2, "0", STR_PAD_LEFT);
    } else {
        $newCode = "HD01";
    }

    echo json_encode(["status" => "success", "newCode" => $newCode]);
    exit;
}

// ==========================
// ✅ LẤY MÃ NHÂN VIÊN ĐĂNG NHẬP
// ==========================
if ($action == 'getCurrentNV') {
    session_start();
    if (isset($_SESSION['ma_nv'])) {
        echo json_encode(["status" => "success", "MaNV" => $_SESSION['ma_nv']]);
    } else {
        echo json_encode(["status" => "error", "message" => "Chưa đăng nhập hoặc chưa có session ma_nv"]);
    }
    exit;
}



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
    header('Content-Type: application/json; charset=utf-8');

    $data = json_decode(file_get_contents("php://input"), true);
    if (!$data) {
        echo json_encode(["status" => "error", "message" => "Dữ liệu JSON không hợp lệ"]);
        exit;
    }

    $MaHD = $data['MaHD'] ?? '';
    $NgayBan = $data['NgayBan'] ?? '';
    $MaNV = $data['MaNV'] ?? '';
    $MaKH = $data['MaKH'] ?? '';
    $MaCH = $data['MaCH'] ?? '';
    $TongTien = $data['TongTien'] ?? 0;

    if ($MaHD === '') {
        echo json_encode(["status" => "error", "message" => "Thiếu mã hóa đơn"]);
        exit;
    }

    // ✅ Sử dụng $conn từ file database
    $sql = "UPDATE tbl_hoadonban 
            SET NgayBan = ?, MaNV = ?, MaKH = ?, MaCH = ?, TongTien = ? 
            WHERE MaHD = ?";
    $stmt = $connect->prepare($sql);

    if (!$stmt) {
        echo json_encode(["status" => "error", "message" => "Lỗi prepare: " . $connect->error]);
        exit;
    }

    $stmt->bind_param("sssids", $NgayBan, $MaNV, $MaKH, $MaCH, $TongTien, $MaHD);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Cập nhật hóa đơn thành công!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Lỗi khi cập nhật: " . $stmt->error]);
    }

    $stmt->close();
    break;


// them
  case 'add':
    $input = json_decode(file_get_contents("php://input"), true);
    $added = $hoadon->add($input);

    if ($added === true) {
    echo json_encode(["status" => "success", "message" => "Thêm hóa đơn thành công"]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Thêm hóa đơn thất bại",
            "error_detail" => $hoadon->lastError ?? "Không rõ nguyên nhân"
        ]);
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
    
    //Xem chi tiết các sản phẩm trong hóa đơn
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
