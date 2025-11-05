<?php
session_start();
include_once "../../Config/db_config.php";
require_once '../../../../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$user = trim($_POST['user']);
if ($user == "") {
    die("Vui lòng nhập email hoặc tên đăng nhập!");
}

// Tìm email tương ứng trong DB
$sql = "SELECT Email 
        FROM tbl_taikhoankhachhang 
        WHERE (Email = '$user' OR TenDangNhap = '$user') 
        AND LoaiDangNhap = 'local'";
$result = $connect->query($sql);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $email = $row['Email'];

    // Tạo mã OTP 6 số
    $otp = rand(100000, 999999);
    $_SESSION['otp'] = $otp;
    $_SESSION['otp_email'] = $email;
    $_SESSION['otp_time'] = time();

    // --- Gửi mail ---
    $mail = new PHPMailer();
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'agutech.store@gmail.com';
        $mail->Password = 'nrdj lfkc sjkv hhzi'; // App password
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;
        $mail->CharSet = 'UTF-8';

        $mail->setFrom('agutech.store@gmail.com', 'Cửa hàng linh kiện điện tử Agu Tech');
        $mail->addAddress($email, $user);

        $mail->isHTML(true);
        $mail->Subject = 'Mã xác nhận đặt lại mật khẩu - Cửa hàng linh kiện điện tử Agu Tech';
        $mail->Body = "
            <p>Xin chào <strong>$user</strong>,</p>
            <p>Bạn vừa yêu cầu đặt lại mật khẩu cho tài khoản của mình.</p>
            <p><strong>Mã OTP của bạn là:</strong> <span style='font-size:20px;'>$otp</span></p>
            <p>Mã này có hiệu lực trong 5 phút.</p>
            <p>Trân trọng,<br>Cửa hàng linh kiện điện tử Agu Tech</p>
        ";

        $mail->send();
        echo "<script>
                alert('Mã OTP đã được gửi đến email: $email');
                window.location.href = 'verify_otp.php';
            </script>";
    } catch (Exception $e) {
        echo "Không thể gửi email: {$mail->ErrorInfo}";
    }
} else {
    echo "Email hoặc tên đăng nhập không tồn tại!";
}
?>
