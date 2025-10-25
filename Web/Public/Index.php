<?php
  session_start();

  // Gọi file cấu hình
  include_once '../API/Config/db_config.php';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>AguTech - Shop Linh Kiện Điện Tử</title>
  <link rel="stylesheet" href="assets/css/Style_main.css">
  <link rel="icon" type="image/ico" href="assets/img/favicon.ico">
</head>
<body>
  <!-- ======= Header chính ======= -->
  <header class="main-header">
    <div class="logo">
      <a href = "#"><img src="assets/img/logo.png" alt="Logo" /></a>
    </div>

    <div class="search-bar">
      <select>
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
      <input type="text" placeholder="Tìm kiếm sản phẩm...">
      <button>🔍</button>
    </div>

    <div class="header-right">
      <div class="cart-area">
        <a href="#"><i class="fas fa-heart"></i><span class="badge">0</span></a>
        <a href="#"><i class="fas fa-shopping-cart"></i><span class="badge">0</span></a>
        <span class="cart-total">0₫</span>
      </div>
      <div class="login-btn">
        <a href="#" data-type="login">Đăng nhập</a>
        <a href="#" data-type="register">Đăng ký</a>
      </div>
    </div>
  </header>

  <!-- ======= Thanh menu chính ======= -->
  <nav class="main-nav">
    <ul>
      <li><a href="#">Trang chủ</a></li>
      <li><a href="#">Sản phẩm</a></li>
      <li><a href="#">Khuyến mãi</a></li>
      <li><a href="#">Tin công nghệ</a></li>
      <li><a href="#">Liên hệ</a></li>
      <li><a href="#">Giới thiệu</a></li>
    </ul>
  </nav>

  <!-- ======= Banner ======= -->
  <!-- <section class="banner">
    <img src="assets/img/banner-electronic.jpg" alt="Banner linh kiện điện tử">
    <div class="banner-text">
      <h2>Linh kiện chất lượng - Giá sinh viên</h2>
      <p>Cung cấp linh kiện Arduino, cảm biến, module, IC... giao hàng toàn quốc!</p>
      <a href="#" class="btn">Mua ngay</a>
    </div>
  </section> -->

  <!-- ======= Danh mục nổi bật ======= -->
  <section class="categories">
    <h2>Danh mục nổi bật</h2>
    <div class="category-grid">
      <div class="category-item">
        <img src="assets/img/cat-arduino.jpg" alt="">
        <h3>Arduino</h3>
      </div>
      <div class="category-item">
        <img src="assets/img/cat-sensor.jpg" alt="">
        <h3>Cảm biến</h3>
      </div>
      <div class="category-item">
        <img src="assets/img/cat-power.jpg" alt="">
        <h3>Nguồn & Pin</h3>
      </div>
      <div class="category-item">
        <img src="assets/img/cat-module.jpg" alt="">
        <h3>Module & IC</h3>
      </div>
    </div>
  </section>

  <!-- ======= Footer ======= -->
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

  <!-- Font Awesome -->
  <script src="https://kit.fontawesome.com/a2e0e6b9f3.js" crossorigin="anonymous"></script>
</body>
</html>
