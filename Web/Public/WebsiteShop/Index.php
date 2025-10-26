<?php
  session_start();

  // Gọi file cấu hình
  include_once '../../API/Config/db_config.php';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <title>AguTech - Shop Linh Kiện Điện Tử</title>
  <link rel="stylesheet" href="../assets/css/Style_main.css?v=2">
  <link rel="icon" type="image/ico" href="../assets/img/favicon.ico">
</head>
<body>
  <!-- ======= Header chính ======= -->
  <header class="main-header">
    <div class="logo">
      <a href = "Index.php?do=Home"><img src="../assets/img/logo.png" alt="Logo" /></a>
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
        <a href="#"><i class="fas fa-heart"></i>Yêu thích</a>
        <a href="#"><i class="fas fa-shopping-cart"></i>Giỏ Hàng</a>
      </div>
      <div class="login-btn">
        <a href="Index.php?do=LoginForm"><i class="fa-regular fa-user fa-bounce"></i></a>
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

  <!-- ======= Main ======= -->
  <?php			
		$do = isset($_GET['do']) ? $_GET['do'] : "Home";			
		include $do . ".php";
	?>

  
  
  

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
  
</body>
</html>
