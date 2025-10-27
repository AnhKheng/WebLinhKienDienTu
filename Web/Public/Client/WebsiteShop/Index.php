<?php
session_start();

include_once 'D:/VertrigoServ/www/WebLinhKienDienTu/Web/API/Config/db_config.php';

?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <title>AguTech - Shop Linh Kiện Điện Tử</title>
  <link rel="stylesheet" href="/WebLinhKienDienTu/Web/Public/Client/assets/css/Style_main.css">
  <link rel="icon" type="image/ico" href="/WebLinhKienDienTu/Web/Public/Client/assets/img/favicon.ico">
</head>
<body>
  <!-- ======= Header chính ======= -->
  <div class="header">
    <header class="main-header">
      <div class="logo">
        <a href="#"><img src="/WebLinhKienDienTu/Web/Public/Client/assets/img/logo.png" alt="Logo" /></a>
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
          <a href="#"><i class="fas fa-heart"></i>Yêu thích</a>
          <a href="#"><i class="fas fa-shopping-cart"></i>Giỏ Hàng</a>
        </div>
        <div class="login-btn">
          <a href="#"><i class="fa-regular fa-user fa-bounce"></i></a>
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
  </div>

  <!-- ======= Sidebar Danh mục ======= -->
  <div class="main">
    <div class="menu-bar">
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
    </div>
    <div class="main-content">
      <!-- ======= Danh mục nổi bật ======= -->
      <section class="categories" id="productSection">
        <h2>Danh mục nổi bật</h2>
        <div class="category-grid" id="contentArea">
          <!-- Nội dung sẽ được tải động bởi JavaScript -->
        </div>
      </section>
    </div>
  </div>

  <!-- ======= Footer ======= -->
  <div class="footer">
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
  </div>

  <!-- Font Awesome -->
  <script src="https://kit.fontawesome.com/a2e0e6b9f3.js" crossorigin="anonymous"></script>
  <script>
    function loadProducts() {
      const categoryId = document.getElementById('categorySelect').value;
      const contentArea = document.getElementById('contentArea');
      const productSection = document.getElementById('productSection');
      const searchInput = document.getElementById('searchInput');

      console.log('Loading products for category:', categoryId); // Debug: Log categoryId

      // Làm rỗng ô tìm kiếm khi chọn danh mục
      if (searchInput) {
        searchInput.value = '';
      }

      fetch('/WebLinhKienDienTu/Web/API/client/Product/get_products.php?category=' + categoryId)
        .then(response => {
          if (!response.ok) {
            throw new Error('Network response was not ok: ' + response.status);
          }
          return response.json();
        })
        .then(data => {
          console.log('Data received:', data); // Debug: Log data
          contentArea.innerHTML = ''; // Xóa nội dung cũ
          if (data.length > 0) {
            productSection.querySelector('h2').textContent = 'Sản phẩm liên quan';
            data.forEach(product => {
              const item = document.createElement('div');
              item.className = 'category-item';
              item.innerHTML = `
                <img src="/WebLinhKienDienTu/Web/Public/Client/assets/img/${product.HinhAnh}" class="category-item img" style="max-height: 150px; width: 100%; object-fit: contain;" alt="${product.TenSP}">
                <h3>${product.TenSP}</h3>
                <p>Giá: ${product.DonGia.toLocaleString('vi-VN')}₫</p>
              `;
              contentArea.appendChild(item);
            });
          } else {
            productSection.querySelector('h2').textContent = 'Danh mục nổi bật';
            contentArea.innerHTML = `
              <div class="category-item">
                <img src="/WebLinhKienDienTu/Web/Public/Client/assets/img/cat-arduino.jpg" class="category-item img" style="max-height: 150px; width: 100%; object-fit: contain;" alt="Arduino">
                <h3>Arduino</h3>
              </div>
              <div class="category-item">
                <img src="/WebLinhKienDienTu/Web/Public/Client/assets/img/cat-sensor.jpg" class="category-item img" style="max-height: 150px; width: 100%; object-fit: contain;" alt="Cảm biến">
                <h3>Cảm biến</h3>
              </div>
              <div class="category-item">
                <img src="/WebLinhKienDienTu/Web/Public/Client/assets/img/cat-power.jpg" class="category-item img" style="max-height: 150px; width: 100%; object-fit: contain;" alt="Nguồn & Pin">
                <h3>Nguồn & Pin</h3>
              </div>
              <div class="category-item">
                <img src="/WebLinhKienDienTu/Web/Public/Client/assets/img/cat-module.jpg" class="category-item img" style="max-height: 150px; width: 100%; object-fit: contain;" alt="Module & IC">
                <h3>Module & IC</h3>
              </div>
            `;
          }
        })
        .catch(error => console.error('Error fetching products:', error)); // Debug: Log error
    }

    function searchProducts() {
      const categoryId = document.getElementById('categorySelect').value;
      const searchInput = document.getElementById('searchInput');
      const searchTerm = searchInput.value.trim().toLowerCase();
      const contentArea = document.getElementById('contentArea');
      const productSection = document.getElementById('productSection');

      console.log('Searching for:', { categoryId, searchTerm }); // Debug: Log search params

      fetch('/WebLinhKienDienTu/Web/API/client/Product/get_products.php?category=' + categoryId + '&search=' + encodeURIComponent(searchTerm))
        .then(response => {
          if (!response.ok) {
            throw new Error('Network response was not ok: ' + response.status);
          }
          return response.json();
        })
        .then(data => {
          console.log('Search data received:', data); // Debug: Log data
          contentArea.innerHTML = ''; // Xóa nội dung cũ
          if (data.length > 0) {
            productSection.querySelector('h2').textContent = 'Kết quả tìm kiếm';
            data.forEach(product => {
              const item = document.createElement('div');
              item.className = 'category-item';
              item.innerHTML = `
                <img src="/WebLinhKienDienTu/Web/Public/Client/assets/img/${product.HinhAnh}" class="category-item img" style="max-height: 150px; width: 100%; object-fit: contain;" alt="${product.TenSP}">
                <h3>${product.TenSP}</h3>
                <p>Giá: ${product.DonGia.toLocaleString('vi-VN')}₫</p>
              `;
              contentArea.appendChild(item);
            });
          } else {
            contentArea.innerHTML = '<p>Không tìm thấy sản phẩm nào.</p>';
          }
        })
        .catch(error => console.error('Error searching products:', error)); // Debug: Log error
    }

    // Load sản phẩm mặc định khi trang tải
    window.onload = function() {
      console.log('Page loaded, calling loadProducts'); // Debug: Log page load
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