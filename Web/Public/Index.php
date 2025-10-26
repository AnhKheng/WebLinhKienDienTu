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
      <a href="#"><img src="assets/img/logo.png" alt="Logo" /></a>
    </div>

    <div class="search-bar">
      <select id="categorySelect" onchange="loadProducts()">
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

  <!-- ======= Phần chính giữa (Danh mục nổi bật hoặc Sản phẩm liên quan) ======= -->
  <main class="main-content">
    <section class="categories" id="productSection">
      <h2>Danh mục nổi bật</h2>
      <div class="category-grid" id="contentArea">
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
  </main>

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
<script>
  function loadProducts() {
    const categoryId = document.getElementById('categorySelect').value;
    const contentArea = document.getElementById('contentArea');
    const productSection = document.getElementById('productSection');
    const searchInput = document.getElementById('searchInput'); // Lấy ô tìm kiếm

    // Làm rỗng ô tìm kiếm khi chọn danh mục
    if (searchInput) {
      searchInput.value = ''; // Đặt giá trị về rỗng
    }

    fetch(`get_products.php?category=${categoryId}`)
      .then(response => response.json())
      .then(data => {
        contentArea.innerHTML = ''; // Xóa nội dung cũ
        if (data.length > 0) {
          productSection.querySelector('h2').textContent = 'Sản phẩm liên quan';
          data.forEach(product => {
            const item = document.createElement('div');
            item.className = 'category-item';
            item.innerHTML = `
              <img src="assets/img/${product.HinhAnh}" class="category-item img" style="max-height: 150px; width: 100%; object-fit: contain;" alt="${product.TenSP}">
              <h3>${product.TenSP}</h3>
              <p>Giá: ${product.DonGia.toLocaleString('vi-VN')}₫</p>
            `;
            contentArea.appendChild(item);
          });
        } else {
          productSection.querySelector('h2').textContent = 'Danh mục nổi bật';
          contentArea.innerHTML = `
            <div class="category-item">
              <img src="assets/img/cat-arduino.jpg" class="category-item img" style="max-height: 150px; width: 100%; object-fit: contain;" alt="">
              <h3>Arduino</h3>
            </div>
            <div class="category-item">
              <img src="assets/img/cat-sensor.jpg" class="category-item img" style="max-height: 150px; width: 100%; object-fit: contain;" alt="">
              <h3>Cảm biến</h3>
            </div>
            <div class="category-item">
              <img src="assets/img/cat-power.jpg" class="category-item img" style="max-height: 150px; width: 100%; object-fit: contain;" alt="">
              <h3>Nguồn & Pin</h3>
            </div>
            <div class="category-item">
              <img src="assets/img/cat-module.jpg" class="category-item img" style="max-height: 150px; width: 100%; object-fit: contain;" alt="">
              <h3>Module & IC</h3>
            </div>
          `;
        }
      })
      .catch(error => console.error('Lỗi:', error));
  }

  function searchProducts() {
    const categoryId = document.getElementById('categorySelect').value;
    const searchInput = document.getElementById('searchInput');
    const searchTerm = searchInput.value.trim().toLowerCase();
    const contentArea = document.getElementById('contentArea');
    const productSection = document.getElementById('productSection');

    fetch(`get_products.php?category=${categoryId}&search=${encodeURIComponent(searchTerm)}`)
      .then(response => response.json())
      .then(data => {
        contentArea.innerHTML = ''; // Xóa nội dung cũ
        if (data.length > 0) {
          productSection.querySelector('h2').textContent = 'Kết quả tìm kiếm';
          data.forEach(product => {
            const item = document.createElement('div');
            item.className = 'category-item';
            item.innerHTML = `
              <img src="assets/img/${product.HinhAnh}" class="category-item img" style="max-height: 150px; width: 100%; object-fit: contain;" alt="${product.TenSP}">
              <h3>${product.TenSP}</h3>
              <p>Giá: ${product.DonGia.toLocaleString('vi-VN')}₫</p>
            `;
            contentArea.appendChild(item);
          });
        } else {
          contentArea.innerHTML = '<p>Không tìm thấy sản phẩm nào.</p>';
        }
      })
      .catch(error => console.error('Lỗi:', error));
  }

  // Load sản phẩm mặc định khi trang tải
  window.onload = function() {
    loadProducts();
  };

  // Liên kết nút tìm kiếm với hàm searchProducts
  document.querySelector('.search-bar button').addEventListener('click', function(event) {
    event.preventDefault(); // Ngăn reload trang
    searchProducts();
  });

  // Thêm sự kiện nhấn Enter trên ô input
  document.getElementById('searchInput').addEventListener('keydown', function(event) {
    if (event.key === 'Enter' || event.keyCode === 13) { // Kiểm tra phím Enter
      event.preventDefault(); // Ngăn gửi form mặc định (nếu có)
      searchProducts();
    }
  });
</script>
</body>
</html>