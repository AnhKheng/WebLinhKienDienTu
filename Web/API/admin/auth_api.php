<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

require_once "../Config/db_config.php";
require_once "../../Includes/Auth.php";

$auth = new Auth($connect);

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {

    // ==== LOGIN ====
    case 'login':
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if ($username === '' || $password === '') {
            echo json_encode(["status" => "error", "message" => "Vui lòng nhập đầy đủ thông tin đăng nhập!"]);
            exit;
        }

        echo json_encode($auth->login($username, $password));
        break;

    // ==== LOGOUT ====
    case 'logout':
        echo json_encode($auth->logout());
        break;

    // ==== LẤY THÔNG TIN USER HIỆN ĐANG LOGIN ====
    case 'get_user':
        echo json_encode($auth->getUserInfo());
        break;

    // ==== TẠO TÀI KHOẢN ====
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

    // ==== XOÁ TÀI KHOẢN ====
    case 'delete_account':
        $data = json_decode(file_get_contents("php://input"), true);
        $MaNV = $data['MaNV'] ?? null;

        if (empty($MaNV)) {
            echo json_encode(["status" => "error", "message" => "Thiếu MaNV để xóa tài khoản!"]);
            exit;
        }

        echo json_encode($auth->deleteAccount($MaNV));
        break;

    // ==== LẤY DANH SÁCH ====
    case 'get_all':
        echo json_encode($auth->getAll());
        break;

    // ==== LẤY 1 TÀI KHOẢN ====
    case 'get_one':
        $MaNV = $_GET['MaNV'] ?? ($_POST['MaNV'] ?? '');
        if ($MaNV === '') {
            echo json_encode(["status" => "error", "message" => "Thiếu MaNV để lấy thông tin!"]);
            exit;
        }
        echo json_encode($auth->getAccountById($MaNV));
        break;

    // ==== CẬP NHẬT TÀI KHOẢN (KHÔNG ĐỔI MẬT KHẨU) ====
    case 'update_account':
        $data = json_decode(file_get_contents("php://input"), true);

        $MaNV = $data['MaNV'] ?? '';
        $TenDangNhap = $data['TenDangNhap'] ?? '';
        $VaiTro = $data['VaiTro'] ?? '';
        $MaCH = $data['MaCH'] ?? '';
        if (!$MaNV || !$TenDangNhap || !$VaiTro) {
            echo json_encode(["status" => "error", "message" => "Thiếu thông tin cập nhật tài khoản!"]);
            exit;
        }

        echo json_encode($auth->updateAccount($MaNV, $TenDangNhap, $VaiTro, $MaCH));
        break;

    // ==== ĐỔI MẬT KHẨU ====
    case 'change_password':
        $data = json_decode(file_get_contents("php://input"), true);
        $MaNV = $data['MaNV'] ?? '';
        $oldPassword = $data['oldPassword'] ?? '';
        $newPassword = $data['newPassword'] ?? '';

        if (!$MaNV || !$oldPassword || !$newPassword) {
            echo json_encode(["status" => "error", "message" => "Thiếu thông tin đổi mật khẩu!"]);
            exit;
        }

        echo json_encode($auth->changePassword($MaNV, $oldPassword, $newPassword));
        break;

    // ==== RESET MẬT KHẨU (MẶC ĐỊNH: 123456) ====
    case 'reset_password':
        $data = json_decode(file_get_contents("php://input"), true);
        $MaNV = $data['MaNV'] ?? '';

        if ($MaNV === '') {
            echo json_encode(["status" => "error", "message" => "Thiếu MaNV để reset mật khẩu!"]);
            exit;
        }

        echo json_encode($auth->resetPassword($MaNV));
        break;

    // ==== ACTION SAI ====
    default:
        echo json_encode(["status" => "error", "message" => "Action không hợp lệ hoặc chưa được gửi!"]);
        break;
}
?>
