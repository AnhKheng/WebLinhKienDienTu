<?php
ob_start();
include_once '../../API/Config/db_config.php';
session_start();
$khachhang = null;
$loi = null;
$makh_from_db = isset($_SESSION['MaKH']) ? $_SESSION['MaKH'] : NULL;
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT kh.TenKH FROM tbl_taikhoankhachhang tk, tbl_khachhang kh WHERE tk.MaKH=kh.MaKH and tk.MaKH = '$id'";
    $result = $connect->query($sql);

    if ($result->num_rows > 0) {
        $khachhang = $result->fetch_assoc();
    } else {
        $loi = "Không tìm thấy khách hàng!";
    }
} else {
    $loi = "Không có mã khách hàng!";
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>AguTech - Shop Linh Kiện Điện Tử</title>

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <script src="https://unpkg.com/alpinejs" defer></script>
  <link rel="stylesheet" href="assets/css/Style_main.css?v=22">
  <link rel="stylesheet" href="assets/css/product_style.css?v=22">
  <link rel="icon" type="image/ico" href="../img/favicon.ico">
</head>
<body>
  <header class="main-header">
    <div class="logo">
      <a href="Index.php?do=Home">
        <img src="../img/logo.png" alt="Logo">
      </a>
    </div>

    <div class="search-bar">
      <select id="categorySelect">
        <option value="featured">--- Danh mục nổi bật ---</option>
        <option value="">Tất cả danh mục</option>
        <?php
          $sql = "SELECT MaDM, TenDM FROM tbl_danhmuc";
          $result = $connect->query($sql);
          if ($result && $result->num_rows > 0) {
              while ($dm = $result->fetch_assoc()) {
                  echo '<option value="' . $dm['MaDM'] . '">' . htmlspecialchars($dm['TenDM']) . '</option>';
              }
          } else {
              echo '<option disabled>Không có danh mục</option>';
          }
        ?>
      </select>
      <input type="text" id="searchInput" placeholder="Tìm kiếm sản phẩm...">
      <button id="searchBtn">🔍</button>
    </div>

    <div class="header-right" x-data="{ openTieuDe: false }">
      <div class="cart-area">
        <a href="#"><i class="fas fa-heart"></i> Yêu thích</a>
        <a href="#"><i class="fas fa-shopping-cart"></i> Giỏ Hàng</a>
      </div>

  <?php if (isset($_SESSION['MaKH'])): ?>
    <?php
      // Lấy thông tin khách hàng đang đăng nhập
      $makh = $_SESSION['MaKH'];
      $sqlKH = "SELECT kh.TenKH 
                FROM tbl_taikhoankhachhang tk
                JOIN tbl_khachhang kh ON tk.MaKH = kh.MaKH
                WHERE tk.MaKH = '$makh'";
      $resultKH = $connect->query($sqlKH);
      $khachhang = $resultKH && $resultKH->num_rows > 0 ? $resultKH->fetch_assoc() : null;
      $tenKH = $khachhang ? $khachhang['TenKH'] : 'Khách hàng';
    ?>

    <div id="taskbar" @click="openTieuDe = !openTieuDe" class="menu-item-flex">
      <img src="assets/img/user.png" class="menu-icon" alt="Avatar">
    </div>

    <div class="submenu" x-show="openTieuDe" x-transition x-cloak>
      <div class="username">
        <?php echo htmlspecialchars($tenKH); ?>
      </div>
      <div class="submenu-item" data-url="WebsiteShop/ThongTinKhachHang.php?id=<?php echo $makh; ?>">
        Thông tin tài khoản
      </div>
      <div class="submenu-item">
        <a href="WebsiteShop/Logout_action.php">Đăng xuất</a>
      </div>
    </div>

    <?php else: ?>
      <div class="login-btn">
        <a href="#" onclick="openLoginForm()"><i class="fa-regular fa-user fa-bounce"></i></a>
      </div>
    <?php endif; ?>
  </div>
</header>

  <nav class="main-nav">
    <ul>
      <li><a href="Index.php?do=Home">Trang chủ</a></li>
      <li><a href="#">Sản phẩm</a></li>
      <li><a href="#">Khuyến mãi</a></li>
      <li><a href="#">Tin công nghệ</a></li>
      <li><a href="#">Liên hệ</a></li>
      <li><a href="#">Giới thiệu</a></li>
    </ul>
  </nav>

  <div class="main">
    <aside class="sidebar">
      <div class="sidebar-header">
        <i class="fas fa-bars"></i> <span>Danh mục sản phẩm</span>
      </div>
      <ul class="sidebar-menu">
        <li><a href="#"><i class="fas fa-laptop"></i> Laptop</a></li>
        <li><a href="#"><i class="fas fa-gamepad"></i> Laptop Gaming</a></li>
        <li><a href="#"><i class="fas fa-microchip"></i> Main, CPU, VGA</a></li>
        <li><a href="#"><i class="fas fa-server"></i> Case, Nguồn, Tản</a></li>
        <li><a href="#"><i class="fas fa-memory"></i> Ổ cứng, RAM, Thẻ nhớ</a></li>
        <li><a href="#"><i class="fas fa-headphones"></i> Loa, Micro, Webcam</a></li>
        <li><a href="#"><i class="fas fa-tv"></i> Màn hình</a></li>
        <li><a href="#"><i class="fas fa-keyboard"></i> Bàn phím</a></li>
        <li><a href="#"><i class="fas fa-mouse"></i> Chuột + Lót chuột</a></li>
        <li><a href="#"><i class="fas fa-headset"></i> Tai nghe</a></li>
        <li><a href="#"><i class="fas fa-chair"></i> Ghế - Bàn</a></li>
        <li><a href="#"><i class="fas fa-network-wired"></i> Phần mềm, mạng</a></li>
        <li><a href="#"><i class="fas fa-gamepad"></i> Handheld, Console</a></li>
        <li><a href="#"><i class="fas fa-plug"></i> Phụ kiện (Hub, sạc...)</a></li>
        <li><a href="#"><i class="fas fa-info-circle"></i> Dịch vụ & thông tin khác</a></li>
      </ul>
    </aside>

    <div class="main-content">
      <?php     
        $do = isset($_GET['do']) ? $_GET['do'] : "Home";     
        include "WebsiteShop/" . $do . ".php";
      ?>
    </div>

    <div id="loginOverlay" class="overlay">
      <div class="overlay-content">
        <?php include "WebsiteShop/LoginForm.php"; ?>
      </div>
    </div>
  </div>

  <footer class="site-footer">
    <div class="footer-info">
      <p>────────────────────────────</p>
      <p><strong>⚙️  Phước Khang — Founder, AguTech</strong></p>
      <p>📧 <a href="mailto:agutech.store@gmail.com">agutech.store@gmail.com</a></p>
      <p>🔧 Linh kiện điện tử | Giải pháp công nghệ</p>
      <p>────────────────────────────</p>
      <p>© 2025 AguTech | All Rights Reserved</p>
    </div>
  </footer>

  <script src="assets/js/loginOverlay.js?v=19"></script>
  <script src="assets/js/loadProducts.js?v=22"></script>
  <script src="assets/js/loadDetails.js?v=22"></script>
</body>
</html>