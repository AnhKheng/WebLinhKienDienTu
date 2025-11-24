<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quên mật khẩu</title>
    <link rel="stylesheet" href="assets/css/Style_account_session.css">
</head>
<body>
<div class="account-container">


    <form action="../../API/client/User/send_otp.php" method="post" class="password-form">
        <h2 class="title">QUÊN MẬT KHẨU</h2>
        <div class="form-group">
            <label for="user">Tên tài khoản hoặc Email</label>
            <input type="text" name="user" placeholder="Nhập tên đăng nhập hoặc email" required>
        </div>

        <button type="submit" class="btn-password">GỬI MÃ OTP</button>
    </form>

    <?php
    if (isset($_SESSION['message'])) {
        echo "<p style='color:red; text-align:center;'>" . $_SESSION['message'] . "</p>";
        unset($_SESSION['message']);
    }
    ?>
</div>
</body>
</html>
