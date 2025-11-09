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
    public function register($TenDangNhap, $MatKhau, $MaNV, $VaiTro) {
        if (!$this->conn) {
            return ["status" => "error", "message" => "Không có kết nối CSDL!"];
        }

        $check = $this->conn->prepare("SELECT * FROM tbl_taikhoan WHERE TenDangNhap = ?");
        if (!$check) {
            return ["status" => "error", "message" => "Lỗi prepare SELECT: " . $this->conn->error];
        }

        $check->bind_param("s", $TenDangNhap);  
        $check->execute();
        $res = $check->get_result();

        if ($res && $res->num_rows > 0) {
            return ["status" => "error", "message" => "Tên đăng nhập đã tồn tại!"];
        }

        $sql = "INSERT INTO tbl_taikhoan (TenDangNhap, MatKhau, MaNV, VaiTro)
                VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
          
            return ["status" => "error", "message" => "Lỗi prepare INSERT: " . $this->conn->error];
        }

        $stmt->bind_param("ssss", $TenDangNhap, $MatKhau, $MaNV, $VaiTro);

        if ($stmt->execute()) {
            return ["status" => "success", "message" => "Tạo tài khoản thành công!"];
        } else {
            return ["status" => "error", "message" => "Không thể tạo tài khoản! Lỗi: " . $stmt->error];
        }
    }
        // ==== XÓA TÀI KHOẢN ==== 
    public function deleteAccount($MaNV) {
        if (!$this->conn) {
            return ["status" => "error", "message" => "Không có kết nối CSDL!"];
        }

        $sql = "DELETE FROM tbl_taikhoan WHERE MaNV = ?";
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            return ["status" => "error", "message" => "Lỗi prepare DELETE: " . $this->conn->error];
        }

        $stmt->bind_param("s", $MaNV);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                return ["status" => "success", "message" => "Xóa tài khoản thành công!"];
            } else {
                return ["status" => "error", "message" => "Không tìm thấy tài khoản cần xóa!"];
            }
        } else {
            return ["status" => "error", "message" => "Lỗi khi xóa tài khoản: " . $stmt->error];
        }
    }
    public function getAll() {
        if (!$this->conn) {
            return ["status" => "error", "message" => "Không có kết nối CSDL!"];
        }

        $sql = "SELECT tk.MaNV, tk.TenDangNhap, tk.VaiTro, nv.TenNV, nv.MaCH
                FROM tbl_taikhoan tk
                JOIN tbl_nhanvien nv ON tk.MaNV = nv.MaNV";

        $result = $this->conn->query($sql);

        if (!$result) {
            return ["status" => "error", "message" => "Lỗi truy vấn: " . $this->conn->error];
        }

        $accounts = [];
        while ($row = $result->fetch_assoc()) {
            $accounts[] = [
                "MaNV" => $row["MaNV"],
                "TenDangNhap" => $row["TenDangNhap"],
                "VaiTro" => $row["VaiTro"],
                "TenNV" => $row["TenNV"],
                "MaCH" => $row["MaCH"]
            ];
        }

        return ["status" => "success", "data" => $accounts];
    }
    public function getAccountById($MaNV) {
        if (!$this->conn) {
            return ["status" => "error", "message" => "Không có kết nối CSDL!"];
        }

        $sql = "SELECT tk.MaNV, tk.TenDangNhap, tk.VaiTro, nv.TenNV, nv.MaCH
                FROM tbl_taikhoan tk
                JOIN tbl_nhanvien nv ON tk.MaNV = nv.MaNV
                WHERE tk.MaNV = ?";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return ["status" => "error", "message" => "Lỗi prepare SELECT: " . $this->conn->error];
        }

        $stmt->bind_param("s", $MaNV);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            return ["status" => "error", "message" => "Không tìm thấy tài khoản!"];
        }

        $row = $result->fetch_assoc();
        return [
            "status" => "success",
            "data" => [
                "MaNV" => $row["MaNV"],
                "TenDangNhap" => $row["TenDangNhap"],
                "VaiTro" => $row["VaiTro"],
                "TenNV" => $row["TenNV"],
                "MaCH" => $row["MaCH"]
            ]
        ];
    }
    // ==== CẬP NHẬT THÔNG TIN TÀI KHOẢN (KHÔNG SỬA MẬT KHẨU) ====
    public function updateAccount($MaNV, $TenDangNhap = null, $VaiTro = null, $MaCH = null) {
        if (!$this->conn) {
            return ["status" => "error", "message" => "Không có kết nối CSDL!"];
        }

        // Kiểm tra tài khoản có tồn tại không
        $checkExist = $this->conn->prepare("SELECT MaNV, TenDangNhap, VaiTro FROM tbl_taikhoan WHERE MaNV = ?");
        $checkExist->bind_param("s", $MaNV);
        $checkExist->execute();
        $existResult = $checkExist->get_result();
        if ($existResult->num_rows === 0) {
            return ["status" => "error", "message" => "Tài khoản không tồn tại!"];
        }
        $current = $existResult->fetch_assoc();

        // Nếu có thay đổi TenDangNhap -> kiểm tra trùng
        if ($TenDangNhap !== null && $TenDangNhap !== $current['TenDangNhap']) {
            $checkDup = $this->conn->prepare("SELECT MaNV FROM tbl_taikhoan WHERE TenDangNhap = ? AND MaNV != ?");
            $checkDup->bind_param("ss", $TenDangNhap, $MaNV);
            $checkDup->execute();
            $dupRes = $checkDup->get_result();
            if ($dupRes && $dupRes->num_rows > 0) {
                return ["status" => "error", "message" => "Tên đăng nhập đã tồn tại cho tài khoản khác!"];
            }
        }

        $this->conn->begin_transaction();

        try {
            // Cập nhật tbl_taikhoan nếu có dữ liệu thay đổi
            if ($TenDangNhap !== null || $VaiTro !== null) {
                $fields = [];
                $params = [];
                $types = "";

                if ($TenDangNhap !== null) {
                    $fields[] = "TenDangNhap = ?";
                    $params[] = $TenDangNhap;
                    $types .= "s";
                }

                if ($VaiTro !== null) {
                    $fields[] = "VaiTro = ?";
                    $params[] = $VaiTro;
                    $types .= "s";
                }

                if (!empty($fields)) {
                    $sql1 = "UPDATE tbl_taikhoan SET " . implode(", ", $fields) . " WHERE MaNV = ?";
                    $stmt1 = $this->conn->prepare($sql1);
                    $types .= "s";
                    $params[] = $MaNV;
                    $stmt1->bind_param($types, ...$params);
                    if (!$stmt1->execute()) throw new Exception("Lỗi khi cập nhật tbl_taikhoan: " . $stmt1->error);
                }
            }

            // Cập nhật MaCH nếu có truyền vào
            if ($MaCH !== null && $MaCH !== '') {
                $stmt2 = $this->conn->prepare("UPDATE tbl_nhanvien SET MaCH = ? WHERE MaNV = ?");
                $stmt2->bind_param("ss", $MaCH, $MaNV);
                if (!$stmt2->execute()) throw new Exception("Lỗi khi cập nhật MaCH: " . $stmt2->error);
            }

            $this->conn->commit();
            return ["status" => "success", "message" => "Cập nhật tài khoản thành công!"];

        } catch (Exception $ex) {
            $this->conn->rollback();
            return ["status" => "error", "message" => $ex->getMessage()];
        }
    }

    // ==== ĐỔI MẬT KHẨU ====
    public function changePassword($MaNV, $oldPassword, $newPassword) {
        if (!$this->conn) {
            return ["status" => "error", "message" => "Không có kết nối CSDL!"];
        }

        // Lấy mật khẩu hiện tại
        $sql = "SELECT MatKhau FROM tbl_taikhoan WHERE MaNV = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return ["status" => "error", "message" => "Lỗi prepare SELECT: " . $this->conn->error];
        }
        $stmt->bind_param("s", $MaNV);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            return ["status" => "error", "message" => "Không tìm thấy tài khoản!"];
        }

        $row = $result->fetch_assoc();
        // Kiểm tra mật khẩu cũ
        if ($oldPassword !== $row['MatKhau']) {
            return ["status" => "error", "message" => "Mật khẩu cũ không đúng!"];
        }

        // Kiểm tra trùng mật khẩu cũ
        if ($newPassword === $row['MatKhau']) {
            return ["status" => "error", "message" => "Mật khẩu mới không được trùng mật khẩu cũ!"];
        }

        // Validate mật khẩu mới (ví dụ >=6 ký tự)
        if (strlen($newPassword) < 6) {
            return ["status" => "error", "message" => "Mật khẩu mới phải ít nhất 6 ký tự!"];
        }

        // Cập nhật mật khẩu mới
        $update = $this->conn->prepare("UPDATE tbl_taikhoan SET MatKhau = ? WHERE MaNV = ?");
        if (!$update) {
            return ["status" => "error", "message" => "Lỗi prepare UPDATE: " . $this->conn->error];
        }
        $update->bind_param("ss", $newPassword, $MaNV);

        if ($update->execute()) {
            return ["status" => "success", "message" => "Đổi mật khẩu thành công!"];
        } else {
            return ["status" => "error", "message" => "Lỗi khi đổi mật khẩu: " . $update->error];
        }
    }

    // ==== RESET MẬT KHẨU THEO MaNV ====
    public function resetPassword($MaNV) {
        if (!$this->conn) {
            return ["status" => "error", "message" => "Không có kết nối CSDL!"];
        }

        // Mật khẩu mặc định
        $defaultPassword = "123456";

        $stmt = $this->conn->prepare("UPDATE tbl_taikhoan SET MatKhau = ? WHERE MaNV = ?");
        if (!$stmt) {
            return ["status" => "error", "message" => "Lỗi prepare UPDATE: " . $this->conn->error];
        }

        $stmt->bind_param("ss", $defaultPassword, $MaNV);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                return ["status" => "success", "message" => "Reset mật khẩu thành công!"];
            } else {
                return ["status" => "error", "message" => "Mậ khẩu đã là mật khẩu mặc định!"];
            }
        } else {
            return ["status" => "error", "message" => "Lỗi khi reset mật khẩu: " . $stmt->error];
        }
    }

    

}
?>
