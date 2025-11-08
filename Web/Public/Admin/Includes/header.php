<div class="header">
  <!-- Ô tìm kiếm -->
  <div class="header-left">
    <div class="search-bar">
      <input type="text" placeholder="Search for..." />
      <button class="search-btn">
        <i class="fas fa-search"></i>
      </button>
    </div>
  </div>

  <!-- Khu vực thông báo + người dùng -->
  <div class="header-right">
    <div class="icons">
      <div class="icon-item">
        <i class="fas fa-bell"></i>
        <span class="badge">3+</span>
      </div>
      <div class="icon-item">
        <i class="fas fa-envelope"></i>
        <span class="badge">7</span>
      </div>
    </div>

    <div class="user-info">
      <span id="userName"></span>
      <span id="userId" hidden></span>
      <span id="userRole" hidden></span>
      <button id="btn-user" class="btn-user" aria-label="User">
        <img src="assets/img/user.png" alt="avatar">
      </button>

      <div id="profile-menu" class="profile-menu hidden">
        <ul>
          <li id="infoBtn"><i class="fas fa-user" ></i> Hồ sơ</li>
          <li id="changePwBtn"><i class="fas fa-cog"></i> Đổi mật khẩu</li>
          <li id="logoutBtn"><i class="fas fa-sign-out-alt"></i> Đăng xuất</li>
        </ul>
      </div>
    </div>
  </div>
</div>
