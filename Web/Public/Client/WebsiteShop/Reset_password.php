<?php
session_start();
include_once "../../../API/Config/db_config.php";

if (!isset($_SESSION['otp_verified']) || $_SESSION['otp_verified'] !== true) {
    die("Bạn chưa xác minh mã OTP.");
}

$email = $_SESSION['otp_email'];

// ✅ Khởi tạo biến tránh lỗi Notice
$pass1 = $pass2 = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pass1 = trim($_POST['password1']);
    $pass2 = trim($_POST['password2']);

    if ($pass1 != $pass2) {
        echo "<script>alert('Mật khẩu xác nhận không khớp!');</script>";
    } else {
        $sql = "UPDATE tbl_taikhoankhachhang SET MatKhau = '$pass1' WHERE Email = '$email'";
        if ($connect->query($sql)) {
            echo "<script>
                alert('Đổi mật khẩu thành công!');
                window.location.href = '../Index.php';
            </script>";
            session_destroy();
            exit;
        } else {
            echo "<script>alert('Lỗi: " . $connect->error . "');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đổi mật khẩu</title>
    <link rel="stylesheet" href="assets/css/Style_account_session.css">
</head>
<body>
<div class="account-container">
    <div class="logo">
        <a href="#"><img src="assets/img/logo.png" alt="Logo" /></a>
    </div>

    <h2 class="title">ĐỔI MẬT KHẨU</h2>

    <form action="" method="post" class="password-form">
        <div class="form-group">
            <label for="password1">Mật khẩu mới</label>
            <input type="password" name="password1" placeholder="Nhập mật khẩu mới" required>
        </div>

        <div class="form-group">
            <label for="password2">Xác nhận mật khẩu</label>
            <input type="password" name="password2" placeholder="Xác nhận mật khẩu" required>
        </div>
       <button type="submit" class="btn-password">CẬP NHẬT MẬT KHẨU</button>
    </form>
</div>
</body>
</html>
