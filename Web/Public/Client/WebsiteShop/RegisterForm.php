<div class="register-container">
    <div class="logo">
        <a href = "#"><img src="../assets/img/logo.png" alt="Logo" /></a>
    </div>
    <h2 class="register-title">ĐĂNG KÝ</h2>

    <form action="/login-action" method="post" class="register-form">
        <div class="form-group">
            <input type="text" name="username" placeholder="Số điện thoại / Email" required>
        </div>

        <div class="form-group">
            <input type="password" name="password" placeholder="Mật khẩu" required>
        </div>

        <button type="submit" class="btn-register">ĐĂNG NHẬP</button>

        <div class="register-links">
            <a href="Index.php?do=Register">Đăng ký</a>
            <a href="Index.php?do=Forgot_Password">Quên mật khẩu</a>
        </div>
    </form>
</div>