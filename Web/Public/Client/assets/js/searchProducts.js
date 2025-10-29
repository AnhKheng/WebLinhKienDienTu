function loadProducts() {
  const danhMuc = document.getElementById("categorySelect").value;
  const search = document.getElementById("searchInput").value;

  fetch(`WebsiteShop/Home.php?danhmuc=${encodeURIComponent(danhMuc)}&search=${encodeURIComponent(search)}`)
    .then(res => res.text())
    .then(html => {
      document.querySelector('.main-content').innerHTML = html;
    })
    .catch(err => console.error('Lỗi tải sản phẩm:', err));
}

// Bắt sự kiện khi người dùng nhấn nút tìm kiếm
document.getElementById('searchBtn').addEventListener('click', loadProducts);

// Khi nhấn Enter trong ô tìm kiếm
document.getElementById('searchInput').addEventListener('keypress', function(e) {
  if (e.key === 'Enter') {
    e.preventDefault();
    loadProducts();
  }
});

