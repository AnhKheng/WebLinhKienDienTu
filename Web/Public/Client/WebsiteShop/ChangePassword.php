<head>
    <link rel="stylesheet" href="assets/css/Style_account_session.css">
</head>
<body>
<div class="account-container">
    <div class="logo">
        <a href = "#"><img src="assets/img/logo.png" alt="Logo" /></a>
    </div>
    <h2 class="title">ĐỔI MẬT KHẨU</h2>

    <form action="/password-action" method="post" class="password-form">
        <div class="form-group">
            <label for="email">Email</label>
            <input type="text" name="email" placeholder="Email" required>
        </div>
        <div class="form-group">
            <label for="password">Mật khẩu</label>
            <input type="password" name="password1" placeholder="Mật khẩu" required>
        </div>

        <div class="form-group">
            <label for="password">Xác nhận mật khẩu</label>
            <input type="password" name="password2" placeholder="Xác nhận mật khẩu" required>
        </div>

        <div class="form-group">
            <label for="otp">Nhập mã OTP</label>
            <input type="text" name="opt-session" placeholder="Nhập mã gồm 6 số" maxlength="6" required>
            <button type="button" id="btn-GetOtp" onclick="sendOTP()">Gửi mã OTP</button>
        </div>

        <button type="submit" class="btn-password">ĐỔI MẬT KHẨU</button>
    </form>
</div>
</body>