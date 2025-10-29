<head>
    <link rel="stylesheet" href="assets/css/Style_account_session.css?v=1">
</head>
<body>
    <div class="account-container">
        <div class="logo">
            <a href = "#"><img src="../img/logo.png" alt="Logo" /></a>
        </div>
        <h2 class="title">ĐĂNG NHẬP</h2>

        <form action="Index.php?do=Login_action" method="post" class="login-form">
            <div class="form-group">
                <label for="username">Tên tài khoản</label>
                <input type="text" name="Username" placeholder="Số điện thoại / Email" required>
            </div>

            <div class="form-group">
                <label for="password">Mật khẩu</label>
                <input type="password" name="Password" placeholder="Mật khẩu" required>
            </div>

            <button type="submit" class="btn-login">ĐĂNG NHẬP</button>

            <div class="links">
                <a href="Index.php?do=RegisterForm">Đăng ký</a>
                <a href="Index.php?do=ChangePassword">Quên mật khẩu</a>
            </div>
        </form>
    </div>
<body>