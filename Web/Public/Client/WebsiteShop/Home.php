<section class="categories" id="productSection">
  <h2>Danh mục nổi bật</h2>
  <div class="category-grid" id="contentArea"></div>
</section>

<script>
  function loadProducts() {
    const categoryId = document.getElementById('categorySelect').value;
    const contentArea = document.getElementById('contentArea');
    const productSection = document.getElementById('productSection');
    const searchInput = document.getElementById('searchInput');

    if (searchInput) searchInput.value = '';

    fetch('/WebLinhKienDienTu/Web/API/client/Product/get_products.php?category=' + categoryId)
      .then(response => response.json())
      .then(data => {
        contentArea.innerHTML = '';
        if (data.length > 0) {
          productSection.querySelector('h2').textContent = 'Sản phẩm liên quan';
          data.forEach(product => {
            const item = document.createElement('div');
            item.className = 'category-item';
            item.innerHTML = `
              <img src="/WebLinhKienDienTu/Web/Public/Client/assets/img/${product.HinhAnh}" style="max-height: 150px; width: 100%; object-fit: contain;" alt="${product.TenSP}">
              <h3>${product.TenSP}</h3>
              <p>Giá: ${product.DonGia.toLocaleString('vi-VN')}₫</p>
            `;
            contentArea.appendChild(item);
          });
        } else {
          productSection.querySelector('h2').textContent = 'Danh mục nổi bật';
          contentArea.innerHTML = `
            <div class="category-item"><img src="../assets/img/cat-arduino.jpg" alt="Arduino"><h3>Arduino</h3></div>
            <div class="category-item"><img src="../assets/img/cat-sensor.jpg" alt="Cảm biến"><h3>Cảm biến</h3></div>
            <div class="category-item"><img src="../assets/img/cat-module.jpg" alt="Module & IC"><h3>Module & IC</h3></div>
            <div class="category-item"><img src="../assets/img/cat-power.jpg" alt="Nguồn & Pin"><h3>Nguồn & Pin</h3></div>
          `;
        }
      })
      .catch(error => console.error('Error fetching products:', error));
  }

  function searchProducts() {
    const categoryId = document.getElementById('categorySelect').value;
    const searchInput = document.getElementById('searchInput');
    const searchTerm = searchInput.value.trim().toLowerCase();
    const contentArea = document.getElementById('contentArea');
    const productSection = document.getElementById('productSection');

    fetch('/WebLinhKienDienTu/Web/API/client/Product/get_products.php?category=' + categoryId + '&search=' + encodeURIComponent(searchTerm))
      .then(response => response.json())
      .then(data => {
        contentArea.innerHTML = '';
        if (data.length > 0) {
          productSection.querySelector('h2').textContent = 'Kết quả tìm kiếm';
          data.forEach(product => {
            const item = document.createElement('div');
            item.className = 'category-item';
            item.innerHTML = `
              <img src="/WebLinhKienDienTu/Web/Public/Client/assets/img/${product.HinhAnh}" style="max-height: 150px; width: 100%; object-fit: contain;" alt="${product.TenSP}">
              <h3>${product.TenSP}</h3>
              <p>Giá: ${product.DonGia.toLocaleString('vi-VN')}₫</p>
            `;
            contentArea.appendChild(item);
          });
        } else {
          contentArea.innerHTML = '<p>Không tìm thấy sản phẩm nào.</p>';
        }
      })
      .catch(error => console.error('Error searching products:', error));
  }

  // Khi trang Home load
  window.onload = loadProducts;
  document.getElementById('searchBtn').addEventListener('click', e => { e.preventDefault(); searchProducts(); });
  document.getElementById('searchInput').addEventListener('keydown', e => {
    if (e.key === 'Enter') { e.preventDefault(); searchProducts(); }
  });
</script>
