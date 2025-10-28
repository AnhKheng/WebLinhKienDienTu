// Đường dẫn API (tự động hoạt động khi đổi vị trí file)
const apiBasePath = '../../API/client/Product/get_products.php';
const imgBasePath = 'assets/img/';

// Khi trang Home load
window.onload = loadProducts;

// Gắn sự kiện tìm kiếm
document.addEventListener('DOMContentLoaded', () => {
  const searchBtn = document.getElementById('searchBtn');
  const searchInput = document.getElementById('searchInput');

  if (searchBtn) searchBtn.addEventListener('click', e => { 
    e.preventDefault(); 
    searchProducts(); 
  });

  if (searchInput) searchInput.addEventListener('keydown', e => {
    if (e.key === 'Enter') { 
      e.preventDefault(); 
      searchProducts(); 
    }
  });
});

function loadProducts() {
  const categorySelect = document.getElementById('categorySelect');
  const categoryId = categorySelect ? categorySelect.value : '';
  const contentArea = document.getElementById('contentArea');
  const productSection = document.getElementById('productSection');
  const searchInput = document.getElementById('searchInput');

  if (searchInput) searchInput.value = '';

  fetch(`${apiBasePath}?category=${categoryId}`)
    .then(response => {
      if (!response.ok) throw new Error('API không phản hồi.');
      return response.json();
    })
    .then(data => {
      contentArea.innerHTML = '';
      if (data && data.length > 0) {
        productSection.querySelector('h2').textContent = 'Sản phẩm liên quan';
        data.forEach(product => {
          const item = document.createElement('div');
          item.className = 'category-item';
          item.innerHTML = `
            <img src="${imgBasePath}${product.HinhAnh}" 
                 alt="${product.TenSP}" 
                 style="max-height: 150px; width: 100%; object-fit: contain;">
            <h3>${product.TenSP}</h3>
            <p>Giá: ${Number(product.DonGia).toLocaleString('vi-VN')}₫</p>
          `;
          contentArea.appendChild(item);
        });
      } else {
        showDefaultCategories(contentArea, productSection);
      }
    })
    .catch(error => {
      console.error('❌ Lỗi khi load sản phẩm:', error);
      showDefaultCategories(contentArea, productSection);
    });
}

function searchProducts() {
  const categorySelect = document.getElementById('categorySelect');
  const categoryId = categorySelect ? categorySelect.value : '';
  const searchInput = document.getElementById('searchInput');
  const searchTerm = searchInput ? searchInput.value.trim().toLowerCase() : '';
  const contentArea = document.getElementById('contentArea');
  const productSection = document.getElementById('productSection');

  fetch(`${apiBasePath}?category=${categoryId}&search=${encodeURIComponent(searchTerm)}`)
    .then(response => {
      if (!response.ok) throw new Error('API không phản hồi.');
      return response.json();
    })
    .then(data => {
      contentArea.innerHTML = '';
      if (data && data.length > 0) {
        productSection.querySelector('h2').textContent = 'Kết quả tìm kiếm';
        data.forEach(product => {
          const item = document.createElement('div');
          item.className = 'category-item';
          item.innerHTML = `
            <img src="${imgBasePath}${product.HinhAnh}" 
                 alt="${product.TenSP}" 
                 style="max-height: 150px; width: 100%; object-fit: contain;">
            <h3>${product.TenSP}</h3>
            <p>Giá: ${Number(product.DonGia).toLocaleString('vi-VN')}₫</p>
          `;
          contentArea.appendChild(item);
        });
      } else {
        contentArea.innerHTML = '<p>Không tìm thấy sản phẩm nào.</p>';
      }
    })
    .catch(error => console.error('❌ Lỗi tìm kiếm sản phẩm:', error));
}

// Hiển thị danh mục mặc định nếu không có sản phẩm
function showDefaultCategories(contentArea, productSection) {
  productSection.querySelector('h2').textContent = 'Danh mục nổi bật';
  contentArea.innerHTML = `
    <div class="category-item"><img src="${imgBasePath}cat-arduino.jpg" alt="Arduino"><h3>Arduino</h3></div>
    <div class="category-item"><img src="${imgBasePath}cat-sensor.jpg" alt="Cảm biến"><h3>Cảm biến</h3></div>
    <div class="category-item"><img src="${imgBasePath}cat-module.jpg" alt="Module & IC"><h3>Module & IC</h3></div>
    <div class="category-item"><img src="${imgBasePath}cat-power.jpg" alt="Nguồn & Pin"><h3>Nguồn & Pin</h3></div>
  `;
}
