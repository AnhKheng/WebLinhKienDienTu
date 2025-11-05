<?php
class Auth {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
        session_start();
    }

    // ==== ĐĂNG NHẬP ====
    public function login($username, $password) {
        $sql = "SELECT tk.*, nv.TenNV, nv.MaCH
                FROM tbl_taikhoan tk
                JOIN tbl_nhanvien nv ON tk.MaNV = nv.MaNV
                WHERE tk.TenDangNhap = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            return ["status" => "error", "message" => "Tài khoản không tồn tại!"];
        }

        $user = $result->fetch_assoc();



        // Kiểm tra mật khẩu (có thể mã hóa về sau)
        if ($password !== $user["MatKhau"]) {
            return ["status" => "error", "message" => "Sai mật khẩu!"];
        }

        // Lưu session
        $_SESSION["username"] = $user["TenDangNhap"];
        $_SESSION["ten_nv"] = $user["TenNV"];
        $_SESSION["vai_tro"] = $user["VaiTro"];
        $_SESSION["ma_nv"] = $user["MaNV"];
        $_SESSION["ma_ch"] = $user["MaCH"];

        return [
            "status" => "success",
            "message" => "Đăng nhập thành công!",
            "data" => [
                "id" => $user["MaNV"],
                "username" => $user["TenNV"],
                "role" => $user["VaiTro"],
                "IdCH"=> $user["MaCH"],
            ]
        ];
    }

    // ==== ĐĂNG XUẤT ====
    public function logout() {
    // Đảm bảo session đã khởi tạo
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Xóa toàn bộ dữ liệu trong session
    $_SESSION = [];

    // Xóa cookie lưu session (nếu có)
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }

    // Hủy session (sau khi đảm bảo nó tồn tại)
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_destroy();
    }

    return [
        "status" => "success",
        "message" => "Đăng xuất thành công!"
    ];
}

    // ==== LẤY THÔNG TIN NGƯỜI DÙNG ====
    public function getUserInfo() {
        if (!isset($_SESSION["username"])) {
            return ["status" => "error", "message" => "Chưa đăng nhập!"];
        }

        return [
            "status" => "success",
            "data" => [
                "TenNV" => $_SESSION["ten_nv"],
                "VaiTro" => $_SESSION["vai_tro"],
                "MaNV" => $_SESSION["ma_nv"],
                "MaCH" => $_SESSION["ma_ch"]
            ]
        ];
    }

    // ==== TẠO TÀI KHOẢN ====
    public function register($TenDangNhap, $MatKhau, $MaNV, $VaiTro, $TrangThai) {
        // Kiểm tra trùng tên đăng nhập
        $check = $this->conn->prepare("SELECT * FROM tbl_taikhoan WHERE TenDangNhap = ?");
        $check->bind_param("s", $TenDangNhap);
        $check->execute();
        $res = $check->get_result();

        if ($res->num_rows > 0) {
            return ["status" => "error", "message" => "Tên đăng nhập đã tồn tại!"];
        }

        $sql = "INSERT INTO tbl_taikhoan (TenDangNhap, MatKhau, MaNV, VaiTro, TrangThai)
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sssss", $TenDangNhap, $MatKhau, $MaNV, $VaiTro, $TrangThai);

        if ($stmt->execute()) {
            return ["status" => "success", "message" => "Tạo tài khoản thành công!"];
        } else {
            return ["status" => "error", "message" => "Không thể tạo tài khoản!"];
        }
    }
}
?>
