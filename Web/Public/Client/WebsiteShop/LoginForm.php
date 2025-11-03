<head>
    <link rel="stylesheet" href="assets/css/Style_account_session.css?v=1">
</head>
<body>
    <div class="account-container">
        <form action="Index.php?do=Login_action" method="post" class="login-form">
            <!-- <div class="logo">
                <a href = "#"><img src="../img/logo.png" alt="Logo" /></a>
            </div> -->

            <h2 class="title">ĐĂNG NHẬP</h2>

            <div class="form-group">
                <label for="username">Tên tài khoản</label>
                <input type="text" name="Username" placeholder="Số điện thoại / Email" required>
            </div>

            <div class="form-group">
                <label for="password">Mật khẩu</label>
                <input type="password" name="Password" placeholder="Mật khẩu" required>
            </div>

            <button type="submit" class="btn-login">ĐĂNG NHẬP</button>
            <div class="google-login">
                <a href="../../API/client/User/login-google.php">
                    <img src="https://developers.google.com/identity/images/btn_google_signin_dark_normal_web.png"
                        alt="Đăng nhập bằng Google"
                        style="width: 220px; margin-top: 10px;">
                </a>
            </div>

            <div class="links">
                <a href="#" onclick="closeLoginForm(); openRegisterForm(); return false;">Đăng ký</a>
                <a href="#" onclick="closeLoginForm(); openPassWordForm(); return false;">Quên mật khẩu</a>
            </div>
        </form>
    </div>
<body>