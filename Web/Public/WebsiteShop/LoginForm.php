<div class="login-container">
    <div class="logo">
        <a href = "#"><img src="../assets/img/logo.png" alt="Logo" /></a>
    </div>
    <h2 class="login-title">ĐĂNG NHẬP</h2>

    <form action="/login-action" method="post" class="login-form">
        <div class="form-group">
            <input type="text" name="username" placeholder="Số điện thoại / Email" required>
        </div>

        <div class="form-group">
            <input type="password" name="password" placeholder="Mật khẩu" required>
        </div>

        <button type="submit" class="btn-login">ĐĂNG NHẬP</button>

        <div class="login-links">
            <a href="/RegisterForm">Đăng ký</a>
            <a href="/Forgot-Password">Quên mật khẩu</a>
        </div>
    </form>
</div>