<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once "../Config/db_config.php";     // file kết nối CSDL
require_once "../../Includes/Auth.php";      // class xử lý logic

$auth = new Auth($connect);

// Nhận action từ GET hoặc POST
$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    // ==== 1️⃣ ĐĂNG NHẬP ====
    case 'login':
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');


        if (empty($username) || empty($password)) {
            echo json_encode(["status" => "error", "message" => "Vui lòng nhập đầy đủ thông tin!"]);
            exit;
        }

        $result = $auth->login($username, $password);
        echo json_encode($result);
        break;


    // ==== 2️⃣ ĐĂNG XUẤT ====
    case 'logout':
        $result = $auth->logout();
        echo json_encode($result);
        break;

    // ==== 3️⃣ LẤY THÔNG TIN NGƯỜI DÙNG ====
    case 'get_user':
        $result = $auth->getUserInfo();
        echo json_encode($result);
        break;

    // ==== 4️⃣ TẠO TÀI KHOẢN MỚI ====
    case 'register':
        $data = json_decode(file_get_contents("php://input"), true);
        $TenDangNhap = trim($data['username'] ?? '');
        $MatKhau = trim($data['password'] ?? '');
        $MaNV = trim($data['MaNV'] ?? '');
        $VaiTro = trim($data['role'] ?? 'nhanvien');

        if ($TenDangNhap === '' || $MatKhau === '' || $MaNV === '') {
        echo json_encode(["status" => "error", "message" => "Thiếu thông tin tạo tài khoản!"]);
        exit;
        }

        echo json_encode($auth->register($TenDangNhap, $MatKhau, $MaNV, $VaiTro));
        break;
    case 'delete_account':
        $data = json_decode(file_get_contents("php://input"), true);
        $MaNV = $data["MaNV"] ?? null;

        if ($MaNV === '') {
            echo json_encode(["status" => "error", "message" => "Thiếu MaNV để xóa tài khoản!"]);
            exit;
        }

        echo json_encode($auth->deleteAccount($MaNV));
        break;
    // ==== ACTION KHÔNG HỢP LỆ ====
    default:
        echo json_encode(["status" => "error", "message" => "Action không hợp lệ hoặc chưa được gửi!"]);
        break;
}
?>
