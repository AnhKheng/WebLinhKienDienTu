<?php
session_start();
if (!isset($_SESSION['otp'])) {
    die("Bạn chưa gửi yêu cầu quên mật khẩu.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $otp_nhap = trim($_POST['otp']);
    $otp_dung = $_SESSION['otp'];
    $otp_time = $_SESSION['otp_time'];

    if (time() - $otp_time > 300) {
        session_destroy();
        die("Mã OTP đã hết hạn. Vui lòng gửi lại yêu cầu.");
    }

    if ($otp_nhap == $otp_dung) {
        $_SESSION['otp_verified'] = true;
        header("Location: ../../../Public/Client/WebsiteShop/Reset_password.php");
        exit;
    } else {
        echo "Mã OTP không chính xác!";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head><meta charset="UTF-8"><title>Xác nhận OTP</title></head>
<body>
    <h3>Nhập mã OTP đã gửi đến email của bạn</h3>
    <form method="POST">
        <input type="text" name="otp" maxlength="6" required>
        <button type="submit">Xác nhận</button>
    </form>
</body>
</html>
