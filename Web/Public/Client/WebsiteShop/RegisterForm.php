<head>
    <link rel="stylesheet" href="assets/css/Style_account_session.css">
</head>
<body>
<div class="account-container">
    <div class="logo">
        <a href = "#"><img src="assets/img/logo.png" alt="Logo" /></a>
    </div>
    <h2 class="title">ĐĂNG KÝ</h2>

    <form action="/register-action" method="post" class="register-form">
        <div class="form-group">
            <label for="username">Tên tài khoản</label>
            <input type="text" name="username" placeholder="Số điện thoại / Tên tài khoản" required>
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="text" name="username" placeholder="Email" required>
        </div>

        <div class="form-group">
            <label for="password">Mật khẩu</label>
            <input type="password" name="password1" placeholder="Mật khẩu" required>
        </div>

        <div class="form-group">
            <label for="password">Xác nhận mật khẩu</label>
            <input type="password" name="password2" placeholder="Xác nhận mật khẩu" required>
        </div>

        <button type="submit" class="btn-register">ĐĂNG KÝ</button>

    </form>
</div>
</body>