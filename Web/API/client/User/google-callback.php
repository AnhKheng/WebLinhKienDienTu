<?php
require_once '../../../../vendor/autoload.php';
require_once '../../Config/db_config.php';
session_start();

$client = new Google_Client();
$client->setClientId('718966963833-1rvf6e4hv3lkuvlt6rs8b24anl7i45vq.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('http://127.0.0.1/WebLinhKienDienTu/Web/API/client/User/google-callback.php');
$client->addScope(['email', 'profile']);

if (isset($_GET['code'])) {
    try {
        $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

        if (isset($token['error'])) {
            throw new Exception('Lỗi xác thực Google: ' . $token['error_description']);
        }

        $client->setAccessToken($token);
        $oauth = new Google_Service_Oauth2($client);
        $google_user = $oauth->userinfo->get();

        $email = $google_user->email;
        $name = $google_user->name;
        
        $maTKKH = null; // Khai báo biến MaTKKH
        $maKH = null;

        // ✅ Lấy MaTKKH và MaKH từ database
        $sql = "SELECT MaTKKH, MaKH FROM tbl_taikhoankhachhang WHERE Email = '$email'";
        $result = mysqli_query($connect, $sql);

        if (!$result) {
            die("Lỗi truy vấn kiểm tra: " . mysqli_error($connect));
        }

        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $maKH = $row['MaKH'];
            $maTKKH = $row['MaTKKH']; // <<< LẤY MaTKKH KHI TÌM THẤY
        } else {

               $query = "SELECT MAX(CAST(SUBSTRING(MaKH, 3) AS UNSIGNED)) AS max_id FROM tbl_khachhang";
                $res = mysqli_query($connect, $query);

                if (!$res) {
                    die("❌ Lỗi truy vấn lấy mã KH: " . mysqli_error($connect));
                }

                $row = mysqli_fetch_assoc($res);
                $nextId = "KH" . str_pad(($row['max_id'] ?? 0) + 1, 2, "0", STR_PAD_LEFT);
                $maKH = $nextId;

                // 🔹 2. Thêm vào bảng khách hàng
                $sql_kh = "INSERT INTO tbl_khachhang (MaKH, TenKH) VALUES ('$maKH', '$name')";
                if (!mysqli_query($connect, $sql_kh)) {
                    die("❌ Lỗi thêm khách hàng: " . mysqli_error($connect));
                }

                // 🔹 3. Thêm vào bảng tài khoản khách hàng (liên kết với KH vừa tạo)
                $sql_tkkh = "INSERT INTO tbl_taikhoankhachhang (MaKH, LoaiDangNhap, TenDangNhap, Email)
                            VALUES ('$maKH', 'google', '$name', '$email')";
                if (!mysqli_query($connect, $sql_tkkh)) {
                    die("❌ Lỗi thêm tài khoản KH: " . mysqli_error($connect));
                }

                // 🔹 4. Lấy mã tài khoản khách hàng vừa tạo
                $maTKKH = mysqli_insert_id($connect);
            }

        // === LƯU SESSION MỚI ===
        $_SESSION['MaKH'] = $maKH;
        $_SESSION['MaTKKH'] = $maTKKH; // <<< THÊM SESSION CHO MaTKKH
        $_SESSION['TenKH'] = $name;
        $_SESSION['Email'] = $email;
        $_SESSION['LoaiDangNhap'] = 'google';

        header("Location: /WebLinhKienDienTu/Web/Public/Client/Index.php?do=Home");
        exit();
    } catch (Exception $e) {
        echo "Lỗi: " . $e->getMessage();
    }
} else {
    echo "Không có mã xác thực từ Google!";
}
?>
